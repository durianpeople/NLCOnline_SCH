<?php
if ($appProp->isMainApp) {
    $request = request("action");
    $controller_path = my_dir("controller/web/$request.php");
    if (file_exists($controller_path) && is_file($controller_path)) {
        $appProp->bundle["view"] = "$request/" . require $controller_path;
    } else {
        $appProp->bundle["view"] = "default/" . require "controller/web/default.php";
    }
}
