<?php

namespace NLC\Base;

use NLC\Throwable\SesiNotFound;
use NLC\Throwable\InvalidTimeInterval;
use Accounts;
use NLC\Throwable\SesiNotDisabled;
use NLC\Throwable\AccessDenied;
use NLC\Throwable\InvalidAction;
use NLC\Throwable\SesiNotStarted;

/**
 * Sesi class
 * 
 * @property-read int $id
 * @property string $name
 * @property int $start_time
 * @property int $end_time
 * @property-read bool $enabled
 * @property Questions $questions
 */
class Sesi
{
    private $id;
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

    #region Static
    public static function create(string $name, int $start_time, int $end_time, bool $is_public)
    {
        if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if ($start_time > $end_time) throw new InvalidTimeInterval;
        if (\Database::insert(
            "app_nlc_sesi",
            [
                (new \DatabaseRowInput)
                    ->setField("name", $name)
                    ->setField("start_time", $start_time)
                    ->setField("end_time", $end_time)
                    ->setField("is_public", $is_public)
            ]
        )) {
            return self::load(\Database::lastId());
        } else return null;
    }

    public static function load(int $id)
    {
        return new self($id);
    }
    #endregion

    private function __construct(int $id)
    {
        if(!Accounts::getAuthLevel(USER_AUTH_REGISTERED)) throw new AccessDenied;
        if (null !== $data = \Database::getRow("app_nlc_sesi", "id", $id)) {
            if ((bool) $data['is_public'] == false) throw new AccessDenied;
            $this->id = (string) $data['id'];
            $this->name = $data['name'];
            $this->start_time = (int) $data['start_time'];
            $this->end_time = (int) $data['end_time'];
            $this->enabled = (bool) $data['enabled'];
            if ($data['questions_id'] != null) $this->questions = Questions::load($data['questions_id']);
        } else throw new SesiNotFound;
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case "start_time":
                if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
                if ($this->enabled == true) throw new SesiNotDisabled;
                $this->start_time = (int) $value;
                $this->commit();
                break;
            case "end_time":
                if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
                if ($this->enabled == true) throw new SesiNotDisabled;
                $this->end_time = (int) $value;
                $this->commit();
                break;
            case "questions":
                if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
                if ($this->enabled == true) throw new SesiNotDisabled;
                if (!($value instanceof Questions) && $value != null) throw new InvalidAction("Value is not of type Questions");
                $this->questions = $value;
                $this->commit();
                break;
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case "id":
                return $this->id;
            case "name":
                return $this->name;
            case "start_time":
                return $this->start_time;
            case "end_time":
                return $this->end_time;
            case "enabled":
                return $this->enabled;
            case "questions":
                if (!Accounts::getAuthLevel(USER_AUTH_EMPLOYEE) && !$this->enrollCheck()) throw new AccessDenied;
                return $this->questions;
        }
    }

    public function removeQuestions()
    {
        if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if ($this->enabled == true) throw new SesiNotDisabled;
        $this->questions = null;
        $this->commit();
    }

    public function enable(): bool
    {
        if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        $this->enabled = true;
        return $this->commit();
    }

    public function disable(): bool
    {
        if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        \Database::execute(
            "DELETE FROM `app_nlc_sesi_user_log` 
            WHERE sesi_id = '?'",
            $this->id
        );
        $this->enabled = false;
        return $this->commit();
    }

    public function enrollCheck(): bool
    {
        $crt = time();
        if ($crt < $this->start_time) return false;
        if ($crt > $this->end_time) return false;
        if ($this->enabled == false) return false;
        return true;
    }

    public function pushAnswer(int $number, int $answer)
    {
        if ($this->enrollCheck()) {
            \Database::execute(
                "INSERT INTO `app_nlc_sesi_user_log` (`sesi_id`, `user_id`, `number`, `answer`) 
                VALUES ('?', '?', '?', '?') 
                ON DUPLICATE KEY 
                UPDATE answer = '?'",
                $this->id,
                Accounts::getUserId(),
                $number,
                $answer,
                $answer
            );
        }
    }

    private function commit(): bool
    {
        $q_fill = ($this->questions === null) ? null : $this->questions->id;
        if (\Database::update(
            "app_nlc_sesi",
            (new \DatabaseRowInput)
                ->setField("questions_id", $q_fill)
                ->setField("enabled", (int) $this->enabled),
            "id",
            $this->id
        )) return true;
        else return false;
    }
}
