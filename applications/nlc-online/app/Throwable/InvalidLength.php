<?php

namespace NLC\Throwable;

class InvalidLength extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("String length exceeds maximum length");
    }
}
