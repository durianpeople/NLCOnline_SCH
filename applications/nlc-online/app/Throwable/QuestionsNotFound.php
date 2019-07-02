<?php

namespace NLC\Throwable;

class QuestionsNotFound extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("Questions not found");
    }
}
