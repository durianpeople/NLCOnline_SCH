<?php

$db = \Database::getRow("app_nlc_survey", "user_id", \PuzzleUser::active()->id);

if ($db === null) {
    $appProp->bundle['eligible'] = true;
} else {
    $appProp->bundle['eligible'] = false;
}

return "main.php";