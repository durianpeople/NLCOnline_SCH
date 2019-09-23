<?php

use NLC\Base\Sesi;

$sc = \Database::getRow("app_nlc_score", "user_id", PuzzleUser::active()->id);

if ($sc) {
    $appProp->bundle['score'][] = [
        "nama_sesi" => Sesi::load($sc['sesi_id'])->name,
        "benar" => $sc['benar'],
        "salah" => $sc['salah'],
        "score" => $sc['score'],
    ];
} else {
    $db = \Database::execute("SELECT s.`name` from app_nlc_sesi s INNER JOIN app_nlc_sesi_whitelist w on w.sesi_id = s.id and w.user_id = '?'", PuzzleUser::active()->id);
    $row = $db->fetch_assoc();
    $appProp->bundle['score'][] = [
        "nama_sesi" => $row['name'],
        "benar" => '0',
        "salah" => '0',
        "score" => '0',
    ];
}
return "main.php";
