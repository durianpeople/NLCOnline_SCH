<?php

namespace NLC\Sesi;

use NLC\Base\Sesi;
use NLC\Base\NLCUser;
use NLC\Enum\SesiStatus;
use NLC\Throwable\SesiNotDisabled;

class SesiPrivate extends Sesi
{
    /**
     * @return self
     */
    public static function create(string $name, int $start_time, int $end_time)
    {
        return parent::create($name, $start_time, $end_time, get_called_class());
    }

    protected function isMeAllowed()
    {
        $db = \Database::execute("SELECT 1 FROM app_nlc_sesi_whitelist WHERE sesi_id = '?' AND `user_id` = '?'", $this->id, \PuzzleUser::active()->id);
        return $db->num_rows > 0;
    }

    public function enrollCheck(): bool
    {
        $crt = time();
        if (
            $crt < $this->start_time ||
            $crt > $this->end_time ||
            $this->enabled == false ||
            !$this->isMeAllowed()
        ) return false;
        return true;
    }

    public function getStatus()
    {
        $crt = time();
        if (!$this->isMeAllowed()) return SesiStatus::NotAllowed;
        if ($crt < $this->start_time) return SesiStatus::NotStarted;
        if ($crt > $this->start_time && $crt < $this->end_time) return SesiStatus::Ongoing;
        return SesiStatus::Done;
    }

    /**
     * @param int[] $ids
     */
    public function setWhitelist(array $ids): bool
    {
        if (!\PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
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

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            "whitelisted" => $this->getWhitelist()
        ]);
    }
}
