<?php
if ($tmpl->http_code == 403) {
    if (PuzzleUser::isAccess(USER_AUTH_REGISTERED)) {
        $tmpl->http_code = 404;
    } else
        redirect("users?redir=/" . urlencode(__HTTP_REQUEST));
}
if ($tmpl->http_code == 404) { 
    require "error.php";
} else {
    if (PuzzleUser::check()) {
        require "html.php";
    } else {
        require "login.php";
    }
}
