<?php

use NLC\Base\Announcement;

switch ($_POST["act"]) {
    case "fetch":
        json_out(Announcement::list());
    case "mark":
        $a = Announcement::load($_POST['id']);
        json_out($a->markAsRead());
}

switch (request(2)) {
    default:
        Template::setSubTitle("Pengumuman");
        return "main.php";
}
