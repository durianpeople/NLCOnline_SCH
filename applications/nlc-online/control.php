<?php

use NLC\Base\NLCUser;

if ($appProp->isMainApp && !is_cli()) {
    define("_PRODUCTION", file_exists(__ROOTDIR . "/production"));

    if (PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) {
        $a = "admin";
        $d = "sesi";
    } else if (PuzzleUser::isAccess(USER_AUTH_REGISTERED)) {
        try {
            NLCUser::getById(PuzzleUser::active()->id);
        } catch (\Throwable $e) {
            echo "Please login using NLC Account!";
            PuzzleUser::logout();
            abort(403);
        }
        $a = "user";
        $d = "sesi";
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
            $appProp->bundle["view"] = "$a/$request/" . require $controller_path;
        } else {
            if (file_exists(my_dir("controller/uni/$request.php"))) {
                $appProp->bundle["view"] = "uni/$request/" . require "controller/uni/$request.php";
            } else {
                $appProp->bundle["view"] = "$a/$d/" . require "controller/$a/$d.php";
            }
        }
    } catch (\Throwable $e) {
        abort(400, _PRODUCTION && !PuzzleUser::isAccess(USER_AUTH_EMPLOYEE) ? "Cannot fulfill your request" : $e->getMessage(), false);
        json_out([
            "error" => $e instanceof \DatabaseError ? "Error" : $e->getMessage()
        ]);
    }
} else {
    PuzzleCLI::register(function ($io, $args) {
        if ($args["reguser"]) {
            NLCUser::create($args["--pass"], $args["--namatim"], $args["--email"], $args["--nlcid"]);
            $io->out("User created!\n");
        }
    });
}
