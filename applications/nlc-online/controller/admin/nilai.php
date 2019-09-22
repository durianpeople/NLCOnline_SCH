<?php

use NLC\Base\NLCUser;
use NLC\Base\Sesi;

$db = \Database::execute("SELECT sesi_id, n.`user_id`, benar, salah, score, nlc_id from app_nlc_score s left join app_nlc_user_nlc_id n on s.user_id = n.user_id");

while($row = $db->fetch_assoc()) {
    $appProp->bundle['score'][] = [
        "nama_sesi" => Sesi::load($row['sesi_id'])->name,
        "nlc_id" => $row['nlc_id'],
        "benar" => $row['benar'],
        "salah" => $row['salah'],
        "score" => $row['score'],
    ];
}
return "main.php";