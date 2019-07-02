<?php

namespace NLC\Throwable;

class InvalidTimeInterval extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("Invalid time interval");
    }
}
