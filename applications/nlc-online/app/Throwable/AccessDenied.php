<?php

namespace NLC\Throwable;

class AccessDenied extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("Access denied");
    }
}
