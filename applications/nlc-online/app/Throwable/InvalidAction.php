<?php

namespace NLC\Throwable;

class InvalidAction extends \PuzzleError
{
    public function __construct($m)
    {
        return parent::__construct("Invalid action: $m");
    }
}
