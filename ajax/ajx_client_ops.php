<?php
$file = './services/'.data::$get->type.'/ajx_'.data::$get->do.'.php';
if (file_exists($file)) require $file;
else create_error('File Not Found');