<?php
switch (request(2)) {
    default:
        Template::setSubTitle("Petunjuk Warm Up" . $sesi->name);
        return "main.php";
}
