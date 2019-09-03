<?php

use NLC\Base\Sesi;
use NLC\Base\Questions;

$s = Sesi::load(1);

var_dump($s->retrieveAnswer());

exit;
return "default";
