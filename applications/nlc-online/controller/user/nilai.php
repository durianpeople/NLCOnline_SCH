<?php

use NLC\Base\Sesi;

$sc = \Database::getRow("app_nlc_score", "user_id", PuzzleUser::active()->id);

$appProp->bundle['score'][] = [
    "nama_sesi" => Sesi::load($sc['sesi_id'])->name,
    "benar" => $sc['benar'],
    "salah" => $sc['salah'],
    "score" => $sc['score'],
];

return "main.php";