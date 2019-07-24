<?php

namespace NLC\Throwable;

class SesiNotStarted extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("Sesi not started");
    }
}
