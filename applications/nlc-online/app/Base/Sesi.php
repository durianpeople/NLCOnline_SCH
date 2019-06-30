<?php

namespace NLC\Base;

class Sesi
{
    private $name;
    private $start_time;
    private $end_time;
    private $questions;

    public function __construct($id)
    { }

    public static function create(string $name, int $start_time, int $end_time, Questions $questions)
    { }
}
