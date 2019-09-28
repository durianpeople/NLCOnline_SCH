<?php

use NLC\Base\Sesi;
use NLC\Sesi\SesiSelfJoin;

switch ($_POST["act"]) {
    case "fetch":
        json_out(Sesi::list(true));
    case "join":
        /** @var SesiSelfJoin $s */
        $s = Sesi::load($_POST['id']);
        if ($s instanceof SesiSelfJoin) {
            $s->enrollMe();
            json_out(Sesi::list(true));
        } else json_out(false);
}

switch (request(2)) {
    default:
        Template::setSubTitle("Pilih Sesi");
        return "main.php";
}

