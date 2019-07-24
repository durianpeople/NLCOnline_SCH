<?php

namespace NLC\Throwable;

class SesiNotDisabled extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("Sesi not disabled");
    }
}
