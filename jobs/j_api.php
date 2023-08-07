<?php
header("Content-Type: application/json");
$out = new json_out();
if (data::$get->all[1]) {
    $file = './services/'.data::$get->all[1].'/ai.php';
    if (file_exists($file)) require $file;
} else
{
    $out->set_output_to_normal_json();
}
$out->join(['jr'=>data::$get->all[1]])->out();
die();


