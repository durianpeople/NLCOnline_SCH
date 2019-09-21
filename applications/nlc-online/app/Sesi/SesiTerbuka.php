<?php

namespace NLC\Sesi;

use NLC\Base\Sesi;
use NLC\Enum\SesiStatus;

class SesiTerbuka extends Sesi
{
    /**
     * @return self
     */
    public static function create(string $name, int $start_time, int $end_time)
    {
        return parent::create($name, $start_time, $end_time, self::class);
    }

    public function enrollCheck(): bool
    {
        $crt = time();
        if ($crt < $this->start_time ||
            $crt > $this->end_time ||
            $this->enabled == false) return false;
        return true;
    }

    public function getStatus()
    {
        $crt = time();
        if ($crt > $this->start_time && $crt < $this->end_time) return SesiStatus::Ongoing;
        if ($crt < $this->start_time) return SesiStatus::NotStarted;
        return SesiStatus::Done;
    }
}