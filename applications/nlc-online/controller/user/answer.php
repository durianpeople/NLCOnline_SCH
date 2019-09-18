<?php
use NLC\Base\Sesi;

switch ($_POST["act"]) {
    case "j":
        $s = Sesi::load($_POST["i"]);
        json_out($s->pushAnswer($_POST["n"],$_POST["a"]));
}

$sesi = $appProp->bundle["sesi"] = Sesi::load($_GET["s"]);
$soal = $appProp->bundle["soal"] = $sesi->questions;

if(!$sesi->enrollCheck()) abort(403, "Please enroll first!");

switch (request(2)) {
    default:
        Template::setSubTitle("Lembar Jawaban " . $sesi->name);
        return "main.php";
}
