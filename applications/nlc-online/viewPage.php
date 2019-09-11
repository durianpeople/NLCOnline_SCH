<?php
if (isset($view)) { // admin//main.php
    if (file_exists($f = my_dir("view/$view"))) include $f;
}
