<?php

switch ($_POST["act"]) {
    
}

switch (request(2)) {
    default:
        Template::setSubTitle("Pilih Sesi");
        return "main.php";
}
