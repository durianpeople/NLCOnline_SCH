<?php

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

}

switch (request(2)) {
    default:
        Template::setSubTitle("Manage Session");
        return "main.php";
}
