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
use NLC\Base\NLCUser;
use NLC\Enum\SesiType;

/**
 * Sesi class
 * 
 * @property-read int $id
 * @property-read bool $enabled
 * @property string $name
 * @property int $start_time
 * @property int $end_time
 * @property-read int $type
 * @property-read int $quota
 * @property Questions $questions
 */
class Sesi implements \JsonSerializable
{
    private static $singleton = [];

    private $id;
    private $name;
    private $start_time;
    private $end_time;
    private $enabled;
    private $type;
    private $quota;
    /**
     * Questions assigned to this class
     *
     * @var Questions $questions
     */
    private $questions = null;

    #region Static
    public static function create(string $name, int $start_time, int $end_time, SesiType $type, int $quota = 0)
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if ($start_time > $end_time) throw new InvalidTimeInterval;
        if ($type == SesiType::SELFJOIN && $quota == 0) throw new InvalidAction("Self join require quota");
        if ($type != SesiType::SELFJOIN) $quota = 0;
        if (\Database::insert(
            "app_nlc_sesi",
            [
                (new \DatabaseRowInput)
                    ->setField("name", $name)
                    ->setField("start_time", $start_time)
                    ->setField("end_time", $end_time)
                    ->setField("type", (int) $type)
                    ->setField("quota", (int) $quota)
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
            if ((int) $data['type'] != SesiType::OPEN && !PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) {
                if((int) $data['type'] == SesiType::WHITELIST) {
                    $db = \Database::execute("SELECT 1 FROM app_nlc_sesi_whitelist WHERE sesi_id = '?' AND `user_id` = '?'", $id, PuzzleUser::active()->id);
                    if (mysqli_num_rows($db) == 0) throw new AccessDenied;
                }
            }
            $this->id = (string) $data['id'];
            $this->name = $data['name'];
            $this->start_time = (int) $data['start_time'];
            $this->end_time = (int) $data['end_time'];
            $this->enabled = (bool) $data['enabled'];
            $this->type = (int) $data['type']; 
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

    /**
     * @param int[] $ids
     */
    public function setWhitelist(array $ids): bool
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if ($this->enabled) throw new SesiNotDisabled;
        \Database::delete("app_nlc_sesi_whitelist", "sesi_id", $this->id);
        $payload = [];
        foreach ($ids as $id) {
            $payload[] = (new \DatabaseRowInput)
                ->setField("sesi_id", $this->id)
                ->setField("user_id", $id);
        }
        return (bool) (\Database::insert("app_nlc_sesi_whitelist", $payload, true));
    }
    /**
     * 
     *
     * @return \NLC\Base\NLCUser[]
     */
    public function getWhitelist()
    {
        $ids = [];
        $db = \Database::execute("SELECT `user_id` FROM app_nlc_sesi_whitelist WHERE sesi_id = '?'", $this->id);
        while ($row = $db->fetch_assoc()) {
            $ids[] = NLCUser::getById($row['user_id']);
        }
        return $ids;
    }

    public function enrollCheck(): bool
    {
        $crt = time();
        if ($crt < $this->start_time ||
            $crt > $this->end_time ||
            $this->enabled == false) return false;
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
        if(PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)){
            return [
                "id" => (int) $this->id,
                "name" => $this->name,
                "start_time" => $this->start_time,
                "end_time" => $this->end_time,
                "enabled" => $this->enabled,
                "type" => $this->type,
                "quota" => $this->quota,
                "questions" => $this->questions ?? null,
                "whitelisted" => $this->getWhitelist(),
            ];
        }else{
            return [
                "id" => (int) $this->id,
                "name" => $this->name,
                "start_time" => $this->start_time,
                "end_time" => $this->end_time,
                "enabled" => $this->enabled,
                // "is_public" => $this->is_public,
                "questions" => $this->questions ?? null
            ];   
        }
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
