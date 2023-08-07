<?php
spl_autoload_register(
    function ($class) {
        $file_name1 = './classes/'.$class.'.php';
        $file_name2 = './abstract/'.$class.'.php';
        if (file_exists($file_name1)) require $file_name1;
        elseif (file_exists($file_name2)) require $file_name2;
        else throw new Exception('Not Found : '.$class);
    }
);
foreach (glob('./includes/*.php') as $item) {
    require $item;
}
