<?php

namespace NLC\Base;

use NLC\Throwable\SesiNotFound;
use NLC\Throwable\InvalidTimeInterval;
use NLC\Throwable\SesiNotDisabled;
use NLC\Throwable\AccessDenied;
use NLC\Throwable\InvalidAction;
use PuzzleUser;

/**
 * Sesi class
 * 
 * @property-read int $id
 * @property-read bool $enabled
 * @property string $name
 * @property int $start_time
 * @property int $end_time
 * @property Questions $questions
 */
abstract class Sesi implements \JsonSerializable
{
    private static $singleton = [];

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
    abstract public function enrollCheck(): bool;
    protected function afterLoad()
    { }

    protected static function create(string $name, int $start_time, int $end_time, string $class_type)
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
                    ->setField("type", $class_type)
            ]
        )) {
            return self::load(\Database::lastId());
        } else {
            throw new \Exception("Tidak dapat membuat sesi baru");
        }
    }

    /**
     * @return Sesi[]
     */
    public static function list()
    {
        $ss = [];
        $db = \Database::execute("SELECT id FROM app_nlc_sesi");
        while ($row = $db->fetch_assoc()) {
            try {
                $ss[] = self::load($row['id']);
            } catch (\Exception $e) { }
        }
        return $ss;
    }

    public static function load(int $id)
    {
        if (null !== $data = \Database::getRow("app_nlc_sesi", "id", $id)) {
            return self::$singleton[$id] ?? self::$singleton[$id] = new $data["type"]($id);
        } else {
            throw new SesiNotFound;
        }
    }

    final public function __construct(int $id)
    {
        if (!PuzzleUser::isAccess(USER_AUTH_REGISTERED)) throw new AccessDenied;
        if (null !== $data = \Database::getRow("app_nlc_sesi", "id", $id)) {
            $this->id = (string) $data['id'];
            $this->name = $data['name'];
            $this->start_time = (int) $data['start_time'];
            $this->end_time = (int) $data['end_time'];
            $this->enabled = (bool) $data['enabled'];
            if ($data['questions_id'] != null) $this->questions = Questions::load($data['questions_id']);
            $this->afterLoad();
        } else throw new SesiNotFound;
    }

    public function __set($name, $value)
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if ($this->enabled == true) throw new SesiNotDisabled;
        switch ($name) {
            case "name":
                $this->name = $value;
                break;
            case "start_time":
                $this->start_time = (int) $value;
                break;
            case "end_time":
                $this->end_time = (int) $value;
                break;
            case "questions":
                if (!($value instanceof Questions) && $value !== null) throw new InvalidAction("Value is not of type Questions");
                $this->questions = $value;
                break;
            default:
                throw new \Exception("Invalid input $name");
        }
        $this->commit();
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
                if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE) && !$this->enrollCheck()) return null;
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
        if ($this->questions == null) throw new InvalidAction("Sesi should be assigned Questions");
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
            "id" => $this->__get("id"),
            "name" => $this->__get("name"),
            "start_time" => $this->__get("start_time"),
            "end_time" => $this->__get("end_time"),
            "enabled" => $this->__get("enabled"),
            "questions" => $this->__get("questions"),
            "type"=>get_class($this)
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
