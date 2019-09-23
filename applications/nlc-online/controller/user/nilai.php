<?php

use NLC\Base\Sesi;

$sc = \Database::getRow("app_nlc_score", "user_id", PuzzleUser::active()->id);

if ($sc) {
    try {
        $appProp->bundle['score'][] = [
            "nama_sesi" => Sesi::load($sc['sesi_id'])->name,
            "benar" => $sc['benar'],
            "salah" => $sc['salah'],
            "score" => $sc['score'],
        ];
    } catch (\Throwable $e) { 
        $appProp->bundle['score'][] = [
            "nama_sesi" => Sesi::load($sc['sesi_id'])->name,
            "benar" => $sc['benar'],
            "salah" => $sc['salah'],
            "score" => $sc['score'],
        ];
    }
} else {
    $appProp->bundle['score'][] = [
        "nama_sesi" => '-',
        "benar" => '-',
        "salah" => '-',
        "score" => '-',
    ];
}
return "main.php";
