<?php

namespace NLC\Throwable;

class SesiExpired extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("Sesi expired");
    }
}
