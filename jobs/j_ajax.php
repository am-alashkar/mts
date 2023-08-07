<?php
job::clear_html();
if (!data::$get->todo) {
    create_error('Ajax Response empty');
}
$ajaxfile = './ajax/ajx_'.data::$get->todo.'.php';
if (file_exists($ajaxfile)) require $ajaxfile;
else print '<i class="fas fa-exclamation-triangle text-red"> '.data::$get->todo.'</i>';
