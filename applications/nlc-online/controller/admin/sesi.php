<?php

use NLC\Base\Questions;
use NLC\Base\Sesi;

switch ($_POST["act"]) {
    case "fetch":
        json_out(Sesi::list());
    case "en_toggle":
        $s = Sesi::load($_POST['id']);
        $s->enabled ? $s->disable() : $s->enable();
        json_out($s->enabled);
    case "set_name":
        $s = Sesi::load($_POST['id']);
        $s->name = $_POST['name'];
        json_out(true);
    case "set_soal":
        $s = Sesi::load($_POST['id']);
        if ($_POST['q_id'] == "null") {
            $q = $s->questions = null;
        } else {
            $q = $s->questions = Questions::load($_POST['q_id']);
        }
        json_out($q);
    case "set_start_time":
        $s = Sesi::load($_POST["id"]);
        json_out($s->start_time = (int) $_POST['start_time']);
    case "set_end_time":
        $s = Sesi::load($_POST["id"]);
        json_out($s->end_time = (int) $_POST['end_time']);
}

switch (request(2)) {
    default:
        Template::setSubTitle("Manage Session");
        return "main.php";
}
