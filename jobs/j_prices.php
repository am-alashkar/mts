<?php
redirect_to_index();
if (!data::$get->all[1]) {
    redirect_to_index();
}
$file = './admincp/pa_'.data::$get->all[1].'.php';
if (file_exists($file)) require $file;
else redirect_to_index();