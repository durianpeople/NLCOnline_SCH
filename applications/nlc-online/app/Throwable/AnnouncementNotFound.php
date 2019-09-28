<?php

namespace NLC\Throwable;

class AnnouncementNotFound extends \PuzzleError
{
    public function __construct()
    {
        return parent::__construct("Announcement not found");
    }
}
