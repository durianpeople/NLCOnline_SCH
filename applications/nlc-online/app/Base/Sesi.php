<?php

namespace NLC\Base;

use NLC\Throwable\SesiNotFound;
use NLC\Throwable\InvalidTimeInterval;
use Accounts;
use NLC\Throwable\SesiNotDisabled;
use NLC\Throwable\AccessDenied;
use NLC\Throwable\InvalidAction;
use NLC\Throwable\SesiNotStarted;
use PuzzleUser;

/**
 * Sesi class
 * 
 * @property-read int $id
 * @property-read bool $enabled
 * @property string $name
 * @property int $start_time
 * @property int $end_time
 * @property-read bool $is_public
 * @property Questions $questions
 */
class Sesi implements \JsonSerializable
{
    private $id;
    private $name;
    private $start_time;
    private $end_time;
    private $enabled;
    private $is_public;
    /**
     * Questions assigned to this class
     *
     * @var Questions $questions
     */
    private $questions = null;

    #region Static
    public static function create(string $name, int $start_time, int $end_time, bool $is_public)
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if ($start_time > $end_time) throw new InvalidTimeInterval;
        if (\Database::insert(
            "app_nlc_sesi",
            [
                (new \DatabaseRowInput)
                    ->setField("name", $name)
                    ->setField("start_time", $start_time)
                    ->setField("end_time", $end_time)
                    ->setField("is_public", (int) $is_public)
            ]
        )) {
            return self::load(\Database::lastId());
        } else return null;
    }

    public static function load(int $id)
    {
        return new self($id);
    }

    /**
     * 
     *
     * @return Sesi[]
     */
    public static function list(): array
    {
        $ss = [];
        $db = \Database::execute("SELECT id FROM app_nlc_sesi");
        while ($row = $db->fetch_assoc()) {
            try {
                $ss[] = new self($row['id']);
            } catch (\Exception $e) { }
        }
        return $ss;
    }
    #endregion

    private function __construct(int $id)
    {
        if (!PuzzleUser::isAccess(USER_AUTH_REGISTERED)) throw new AccessDenied;
        if (null !== $data = \Database::getRow("app_nlc_sesi", "id", $id)) {
            if ((bool) $data['is_public'] == false && !PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
            $this->id = (string) $data['id'];
            $this->name = $data['name'];
            $this->start_time = (int) $data['start_time'];
            $this->end_time = (int) $data['end_time'];
            $this->enabled = (bool) $data['enabled'];
            $this->is_public = (bool) $data['is_public'];
            if ($data['questions_id'] != null) $this->questions = Questions::load($data['questions_id']);
        } else throw new SesiNotFound;
    }

    public function __set($name, $value)
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if ($this->enabled == true) throw new SesiNotDisabled;
        switch ($name) {
            case "name":
                $this->name = $value;
                $this->commit();
                break;
            case "start_time":
                $this->start_time = (int) $value;
                $this->commit();
                break;
            case "end_time":
                $this->end_time = (int) $value;
                $this->commit();
                break;
            case "questions":
                if (!($value instanceof Questions) && $value !== null) throw new InvalidAction("Value is not of type Questions");
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
                if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE) && !$this->enrollCheck()) throw new AccessDenied;
                return $this->questions;
        }
    }

    public function removeQuestions()
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if ($this->enabled == true) throw new SesiNotDisabled;
        $this->questions = null;
        $this->commit();
    }

    public function enable(): bool
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        $this->enabled = true;
        return $this->commit();
    }

    public function disable(): bool
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
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
                VALUES ('?', '?', '?', '?')",
                $this->id,
                PuzzleUser::active()->id,
                $number,
                $answer
            );
        }
    }

    public function retrieveAnswer()
    {
        if ($this->enrollCheck()) {
            $db = \Database::execute(
                "SELECT x.sesi_id, x.user_id, x.`number`, x.answer, x.id from app_nlc_sesi_user_log x inner join (
                select sesi_id, user_id, `number`, max(id) max_id from app_nlc_sesi_user_log where sesi_id = '?' and user_id = '?' group by `number`
            ) y
            on x.sesi_id = y.sesi_id and x.user_id = y.user_id and x.`number` = y.`number` and x.`id` = y.max_id where x.sesi_id = '?' and x.user_id = '?';",
                $this->id,
                PuzzleUser::active()->id,
                $this->id,
                PuzzleUser::active()->id
            );
            $obj = [];
            while ($row = $db->fetch_assoc()) {
                $obj[$row['number']] = $row['answer'];
            }
            json_out($obj);
        }
    }

    public function jsonSerialize()
    {
        return [
            "id" => (int) $this->id,
            "name" => $this->name,
            "start_time" => $this->start_time,
            "end_time" => $this->end_time,
            "enabled" => $this->enabled,
            "is_public" => $this->is_public,
            "questions" => $this->questions ?? null,
        ];
    }

    private function commit(): bool
    {
        $q_fill = ($this->questions === null) ? null : $this->questions->id;
        if (\Database::update(
            "app_nlc_sesi",
            (new \DatabaseRowInput)
                ->setField("name", $this->name)
                ->setField("questions_id", $q_fill)
                ->setField("enabled", (int) $this->enabled)
                ->setField("start_time", (int) $this->start_time)
                ->setField("end_time", (int) $this->end_time),
            "id",
            $this->id
        )) return true;
        else return false;
    }
}
