<?php

namespace NLC\Base;

use NLC\Throwable\SesiNotFound;
use NLC\Throwable\InvalidLength;
use NLC\Throwable\InvalidTimeInterval;
use NLC\Throwable\SesiNotStarted;
use NLC\Throwable\SesiExpired;
use Accounts;
use NLC\Throwable\SesiNotDisabled;
use NLC\Throwable\SesiNotEnabled;
use DatabaseRowInput;

class Sesi
{
    private $handle;
    private $name;
    private $start_time;
    private $end_time;
    private $enabled;
    /**
     * Questions assigned to this class
     *
     * @var Questions $questions
     */
    private $questions = null;
    private $enrolled;
    private $answer;
    private const TABLE = "app_nlc_sesi";
    private const USERTABLE = "app_nlc_sesi_user";

    public function __construct(string $handle)
    {
        if (null !== $data = \Database::getRow(self::TABLE, "handle", $handle)) {
            $this->handle = (string) $data['handle'];
            $this->name = $data['name'];
            $this->start_time = (int) $data['start_time'];
            $this->end_time = (int) $data['end_time'];
            $this->enabled = (bool) $data['enabled'];
            if ($data['questions_handle'] != null) $this->questions = new Questions($data['questions_handle']);
            if (empty($db = \Database::getRowByStatement(
                self::USERTABLE,
                "WHERE sesi_handle = '?' AND user_id = '?'",
                $this->handle,
                (int) Accounts::getUserId()
            ))) {
                $this->enrolled = false;
                $this->answer = [];
            } else {
                $this->enrolled = true;
                $this->answer = json_decode($db['answer']);
            }
        } else throw new SesiNotFound;
    }

    public function assignQuestions(Questions $q)
    {
        if ($this->enabled == true) throw new SesiNotDisabled;
        $this->questions = $q;
        $this->commit();
    }

    public function removeQuestions()
    {
        if ($this->enabled == true) throw new SesiNotDisabled;
        $this->questions = null;
        $this->commit();
    }

    public function getInfo(): array
    {
        $data['handle'] = $this->handle;
        $data['name'] = $this->name;
        $data['start_time'] = $this->start_time;
        $data['end_time'] = $this->end_time;
        $data['enrolled'] = $this->enrolled;
        return $data;
    }

    public function enable(): bool
    {
        $this->enabled = true;
        return $this->commit();
    }

    public function disable(): bool
    {
        \Database::execute( // return value?
            "DELETE * FROM app_nlc_sesi_user 
            WHERE sesi_handle = '?' AND `user_id` = '?'",
            $this->handle,
            (int) Accounts::getUserId()
        );
        $this->enabled = false;
        return $this->commit();
    }

    public function enroll(): bool
    {
        $crt = time();
        if ($crt < $this->start_time) throw new SesiNotStarted;
        if ($crt > $this->end_time) throw new SesiExpired;
        if ($this->enabled == false) throw new SesiNotEnabled;
        if ($this->enrolled == false) {
            if (\Database::insert(
                self::USERTABLE,
                [
                    (new \DatabaseRowInput)
                        ->setField("sesi_handle", $this->handle)
                        ->setField("user_id", (int) Accounts::getUserId())
                        ->setField("answer", json_encode($this->answer))
                ]
            )) {
                return true;
            } else return false;
        } else return true;
    }

    public function store(string $answer_json): bool
    {
        $this->answer = json_decode($answer_json);
        \Database::execute( // return value?
            "UPDATE app_nlc_sesi_user SET answer = '?' 
            WHERE `sesi_handle` = '?' AND `user_id` = '?'",
            json_encode($this->answer),
            $this->handle,
            (int) Accounts::getUserId()
        );
        return true;
    }

    private function commit(): bool
    {
        $q_fill = ($this->questions === null) ? null : $this->questions->getHandle();
        if (\Database::update(
            self::TABLE,
            (new \DatabaseRowInput)
                ->setField("questions_handle", $q_fill)
                ->setField("enabled", $this->enabled),
            "handle",
            $this->handle
        )) return true;
        else return false;
    }

    public static function create(string $handle, string $name, int $start_time, int $end_time)
    {
        if (strlen($handle) > 25 || strlen($name) > 50) throw new InvalidLength;
        if ($start_time > $end_time) throw new InvalidTimeInterval;
        if (\Database::insert(
            self::TABLE,
            [
                (new \DatabaseRowInput)
                    ->setField("handle", $handle)
                    ->setField("name", $name)
                    ->setField("start_time", $start_time)
                    ->setField("end_time", $end_time)
                    ->setField("enabled", false)
            ]
        )) {
            return true;
        } else return false;
    }
}
