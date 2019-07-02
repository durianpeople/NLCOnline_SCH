<?php

namespace NLC\Base;

use NLC\Throwable\SesiNotFound;
use NLC\Throwable\InvalidLength;
use NLC\Throwable\InvalidTimeInterval;

class Sesi
{
    private $handle;
    private $name;
    private $start_time;
    private $end_time;
    /**
     * Questions assigned to this class
     *
     * @var Questions $questions
     */
    private $questions = null;
    const TABLE = "app_nlc_sesi";

    public function __construct(string $handle)
    {
        if (null !== $data = \Database::getRow(self::TABLE, "handle", $handle)) {
            $this->handle = (string) $data['handle'];
            $this->name = $data['name'];
            $this->start_time = (int) $data['start_time'];
            $this->end_time = (int) $data['end_time'];
            if ($data['questions_handle'] != null) $this->questions = new Questions($data['questions_handle']);
        } else throw new SesiNotFound;
    }

    public function assignQuestions(Questions $q)
    {
        $this->questions = $q;
        $this->commit();
    }

    public function removeQuestions()
    {
        $this->questions = null;
        $this->commit();
    }

    public function getEndTime(): int
    {
        return $this->end_time;
    }

    private function commit()
    {
        $q_fill = ($this->questions === null) ? null : $this->questions->getHandle();
        if (\Database::update(
            self::TABLE,
            (new \DatabaseRowInput)
                ->setField("questions_handle", $q_fill),
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
            ]
        )) {
            return true;
        } else return false;
    }
}
