<?php

namespace NLC\Enum;

use NLC\Base\Enum;

class SesiStatus extends Enum
{
    const NotStarted = 0;
    const Ongoing = 1;
    const Done = 2;
    const QuotaFull = 3;
    const NotAllowed = 4;
}
