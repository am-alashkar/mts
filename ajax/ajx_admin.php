<?php
$file = './admin_price_ops/'.data::$get->type.'.php';
if (file_exists($file)) require $file;
else print style::error_gritter('لم يتم العثور على العملية المطلوبة');