<?php

use NLC\Base\Announcement;

switch ($_POST["act"]) {
    case "announce":
        json_out(
            Announcement::create($_POST['title'], $_POST['content'])
        );
}

switch (request(2)) {
    default:
        Template::setSubTitle("Pengumuman");
        return "main.php";
}
