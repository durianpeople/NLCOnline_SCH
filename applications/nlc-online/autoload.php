<?php
spl_autoload_register(function ($c) {
    if (($tok = strtok($c, "\\")) == "NLC") {
        $path = my_dir("app/" . btfslash(strtok('')) . ".php");
        if (file_exists($path)) require $path;
    }
});
