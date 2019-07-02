<?php

namespace NLC\Throwable;

class SesiNotFound extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("Sesi not found");
    }
}
