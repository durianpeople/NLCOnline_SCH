<?php

namespace NLC\Throwable;

class QuestionsIsPermanent extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("Questions is permanent");
    }
}
