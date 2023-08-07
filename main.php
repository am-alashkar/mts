<?php

$time = microtime();
error_reporting(E_ALL^E_NOTICE^E_WARNING);
//error_reporting(0);
if (PHP_VERSION_ID < 70300) {
    die('Your PHP is old . please use v 7.3 or later');
}
$home = str_replace('main.php','',$_SERVER['PHP_SELF']);
$home = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$home;
define('_COOKIE_NAME_','MTS');
define('_FORCE_SSL_',true);
define('_HOME_',$home);
define('_DIR_FROM_ROOT_','/mts/'); // absolute path from server url to main page
define('_STYLE_','style1'); // style folder
define('_DEFAULT_LANG_','ar'); // default lang if browser or user lang not detected
define('_DEFAULT_TIMEZONE_','Asia/Baghdad');
define('_SUPER_ADMIN_','1'); // set to 0 to disable
//$acceptLang = ['ar', 'en'];
define('_LANG_LIST_',['ar']); // ['ar', 'en']
// program related

// clean
require 'clean.php';


require 'browser_settings.php';
require 'autoloader.php';



//debug::start();
// start
// data .. cin
try {
    new data();


//$ps = base64_encode('123qwe123!');
//$p = new password('MTIzcXdlMTIzIQ==');
//
//die($p->get_hash());
// database

    new db();
// config ( Site settings )
    new config;
// member
    new member();
// job
    new job();
// cout
    job::out();
} catch (Throwable  $exception)
{
    error_c($exception);
}
//job::
//cron @TODO LATER
//debug::end();
$time2 = microtime();
//if (data::$get->job != 'ajax')
    //print '<div class="no-print" style="position: fixed;z-index: 99999;left: 30%;bottom: 0px; width: 200px;background-color: white">Page in ('.round(($time2-$time) * 1000,0) .'ms )<br>PHP: '.PHP_VERSION.'</div>';
function error_c(Throwable $exception)
{
    if (data::$get->job == 'ajax') die('<span style="color: red; ">'.$exception->getMessage().'</span>');
    $error['code'] = $exception->getCode();
    $error['details'] = $exception->getMessage();
    $error['files'] = phrases::$error_sender[rand(0,count(phrases::$error_sender)-1)];
    require 'error.php';
}