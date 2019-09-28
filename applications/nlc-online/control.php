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
        $d = "petunjuk";
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
        } else if ($args["regusercsv"]) {
            $csvFile = file($args["--file"]);
            ini_set('max_execution_time', 0);
            foreach ($csvFile as $line) {
                $data = str_getcsv($line);
                try {
                    NLCUser::create($data[2], $data[1], $data[3], $data[0]);
                } catch (\Throwable $e) {
                    $io->out("Failed at id " . $data[0] . "\n");
                }
            }
            $io->out("Done!\n");
        } else if ($args["judge"]) {
            \Database::execute("DELETE FROM app_nlc_score");
            $db = \Database::execute("SELECT a.sesi_id, a.user_id, IFNULL(benar, 0) benar, IFNULL(salah, 0) salah, IFNULL(benar, 0)*3-IFNULL(salah, 0) score from (
                SELECT x.sesi_id, x.user_id, count(1) benar
                from app_nlc_sesi_user_log x inner join 
                    (
                        select sesi_id, user_id, `number`, max(id) max_id from app_nlc_sesi_user_log group by user_id, `number`, sesi_id
                    ) y
                    on x.sesi_id = y.sesi_id and x.user_id = y.user_id and x.`number` = y.`number` and x.`id` = y.max_id 
                inner join app_nlc_user_nlc_id nlcid on nlcid.user_id = x.user_id
                inner join app_nlc_sesi ss on x.sesi_id = ss.id
                inner join app_nlc_questions_answerkey qa on ss.questions_id = qa.question_id and qa.`number` = x.`number`
                where qa.answer = x.answer
                group by x.user_id, x.sesi_id
            ) a left outer join (
                SELECT x.sesi_id, x.user_id, count(1) salah
                from app_nlc_sesi_user_log x inner join 
                    (
                        select sesi_id, user_id, `number`, max(id) max_id from app_nlc_sesi_user_log group by user_id, `number`, sesi_id
                    ) y
                    on x.sesi_id = y.sesi_id and x.user_id = y.user_id and x.`number` = y.`number` and x.`id` = y.max_id 
                inner join app_nlc_user_nlc_id nlcid on nlcid.user_id = x.user_id
                inner join app_nlc_sesi ss on x.sesi_id = ss.id
                inner join app_nlc_questions_answerkey qa on ss.questions_id = qa.question_id and qa.`number` = x.`number`
                where qa.answer <> x.answer
                group by x.user_id, x.sesi_id
            ) b
            on a.user_id = b.user_id
            union
            SELECT a.sesi_id, a.user_id, IFNULL(benar, 0) benar, IFNULL(salah, 0) salah, IFNULL(benar, 0)*3-IFNULL(salah, 0) score from (
                SELECT x.sesi_id, x.user_id, count(1) salah
                from app_nlc_sesi_user_log x inner join 
                    (
                        select sesi_id, user_id, `number`, max(id) max_id from app_nlc_sesi_user_log group by user_id, `number`, sesi_id
                    ) y
                    on x.sesi_id = y.sesi_id and x.user_id = y.user_id and x.`number` = y.`number` and x.`id` = y.max_id 
                inner join app_nlc_user_nlc_id nlcid on nlcid.user_id = x.user_id
                inner join app_nlc_sesi ss on x.sesi_id = ss.id
                inner join app_nlc_questions_answerkey qa on ss.questions_id = qa.question_id and qa.`number` = x.`number`
                where qa.answer <> x.answer
                group by x.user_id, x.sesi_id
            ) a left outer join (
                SELECT x.sesi_id, x.user_id, count(1) benar
                from app_nlc_sesi_user_log x inner join 
                    (
                        select sesi_id, user_id, `number`, max(id) max_id from app_nlc_sesi_user_log group by user_id, `number`, sesi_id
                    ) y
                    on x.sesi_id = y.sesi_id and x.user_id = y.user_id and x.`number` = y.`number` and x.`id` = y.max_id 
                inner join app_nlc_user_nlc_id nlcid on nlcid.user_id = x.user_id
                inner join app_nlc_sesi ss on x.sesi_id = ss.id
                inner join app_nlc_questions_answerkey qa on ss.questions_id = qa.question_id and qa.`number` = x.`number`
                where qa.answer = x.answer
                group by x.user_id, x.sesi_id
            ) b
            on a.user_id = b.user_id;
            ");
            $payload = [];
            while ($row = $db->fetch_assoc()) {
                $payload[] = (new \DatabaseRowInput)
                    ->setField("sesi_id", $row['sesi_id'])
                    ->setField("user_id", $row['user_id'])
                    ->setField("benar", $row['benar'])
                    ->setField("salah", $row['salah'])
                    ->setField("score", $row['score']);
            }
            \Database::insert("app_nlc_score", $payload);
            $io->out("Done!\n");
        }
    });
}
