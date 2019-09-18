<?php

namespace NLC\Sesi;

use NLC\Base\Sesi;
use NLC\Enum\SesiStatus;
use NLC\Throwable\SesiNotFound;
use PuzzleUser;


/**
 * @property-write int $quota
 */
class SesiSelfJoin extends SesiPrivate
{
    private $quota;

    public static function create(string $name, int $start_time, int $end_time, int $quota)
    {
        $s = parent::create($name, $start_time, $end_time, self::class);
        if ($s) {
            \Database::insert("app_nlc_sesi_quota", [(new \DatabaseRowInput)->setField("sesi_id", $s->id)->setField("quota", $quota)]);
            return $s;
        }
        throw new SesiNotFound;
    }

    protected function afterLoad()
    {
        $this->quota = (int) \Database::read("app_nlc_sesi_quota", "quota", "sesi_id", $this->id);
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case "quota":
                if (!is_numeric($value)) throw new \InvalidArgumentException("Expecting integer or number");
                if (\Database::update("app_nlc_sesi_quota", (new \DatabaseRowInput)->setField("quota", (int) $value), "sesi_id", $this->id))
                    $this->quota = (int) $value;
                break;
            default:
                return parent::__set($name, $value);
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case "quota":
                return $this->quota;
            default:
                return parent::__get($name);
        }
    }

    public function enrollMe()
    {
        \Database::lock("app_nlc_sesi_whitelist", "WRITE");
        if ($this->getRemainingQuota() > 0) {
            \Database::execute(
                "INSERT into app_nlc_sesi_whitelist (sesi_id, `user_id`) VALUES ('?','?')",
                $this->id,
                PuzzleUser::active()->id
            );
        }
        \Database::unlock();
        return \Database::affectedRows() > 0;
    }

    public function getStatus()
    {
        $crt = time();
        if ($this->getRemainingQuota() <= 0) return SesiStatus::QuotaFull;
        if ($crt < $this->start_time) return SesiStatus::NotStarted;
        if ($crt > $this->start_time && $crt < $this->end_time) return SesiStatus::Ongoing;
        return SesiStatus::Done;
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            "quota" => $this->quota,
            "remaining" => $this->getRemainingQuota(),
            "joined" => $this->isMeAllowed(),
        ]);
    }

    public function getRemainingQuota(): int
    {
        $db = \Database::execute("SELECT COUNT(1) FROM app_nlc_sesi_whitelist WHERE sesi_id = '?'", $this->id);
        return $this->quota - (int) $db->fetch_row()[0];
    }
}
