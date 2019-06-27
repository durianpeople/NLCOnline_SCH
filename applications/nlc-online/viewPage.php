<?php
if (isset($view)) {
    if (file_exists($f = my_dir("view/$view.php"))) include $f;
}
