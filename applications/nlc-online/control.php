<?php
if ($appProp->isMainApp) {
    if (PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) {
        $a = "admin";
        $d = "sesi";
    } else if (PuzzleUser::isAccess(USER_AUTH_REGISTERED)) {
        $a = "user";
    } else {
        $a = "guest";
    }

    try {
        #Simple Middleware
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_POST["_token"] != session_id()) abort(400, "CSRF token is missing!");
        }

        #Actual controller
        $request = request(1);
        $controller_path = my_dir("controller/$a/$request.php");
        if (file_exists($controller_path) && is_file($controller_path)) {
            $appProp->bundle["view"] = "$a/$d/" . require $controller_path;
        } else {
            $appProp->bundle["view"] = "$a/$d/" . require "controller/$a/$d.php";
        }
    } catch (\Throwable $e) {
        abort(500, $e->getMessage());
    }
}
