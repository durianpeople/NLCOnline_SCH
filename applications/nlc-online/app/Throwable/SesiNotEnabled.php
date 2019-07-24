<?php

namespace NLC\Throwable;

class SesiNotEnabled extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("Sesi not enabled");
    }
}
