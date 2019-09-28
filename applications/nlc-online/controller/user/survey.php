<?php

switch ($_POST['act']) {
    case "submit":
        $db = \Database::insert("app_nlc_survey", [
            (new \DatabaseRowInput)
                ->setField("user_id", PuzzleUser::active()->id)
                ->setField("kualitas_soal", $_POST['kualitas'])
                ->setField("pelayanan_panitia", $_POST['pelayanan'])
                ->setField("kepuasan", $_POST['kepuasan'])
        ]);
        json_out(true);
}


$db = \Database::getRow("app_nlc_survey", "user_id", \PuzzleUser::active()->id);

if ($db === null) {
    $appProp->bundle['eligible'] = true;
} else {
    $appProp->bundle['eligible'] = false;
}

return "main.php";
