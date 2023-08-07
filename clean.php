<?php
if (strstr($_SERVER['REQUEST_URI'],'<') || strstr($_SERVER['REQUEST_URI'],'>')
    || strstr($_SERVER['REQUEST_URI'],'\'') || strstr($_SERVER['REQUEST_URI'],'"')) {
    $error['code'] = 403;
    $error['details'] = 'You tried to use an invalid link';
    $error['files'] = 'a turtle 🐎';
    require 'error.php';
}
$error['files'] = 'a rabbit 🐢';
$error['details'] = "Request Forbidden.";
//unset($GLOBALS);
if (isset($_REQUEST['GLOBALS']) || isset($_COOKIE['GLOBALS'])){
    $error['code'] = 'CD001';
    require('error.php');
}
foreach (array_merge(array_keys($_POST),array_keys($_COOKIE), array_keys($_GET), array_keys($_FILES)) as $key) {
    if (is_numeric($key)) {
        require('error.php');
    }
}
$error['code'] = 'CD004';
unset($_GET);
$error['files'] = 'a rabbit 🐢';
$error['details'] = "Request Forbidden.";

//if (_HOME_ != '/')  $_SERVER['REQUEST_URI'] = str_replace(_HOME_,'',$_SERVER['REQUEST_URI']);
if (_DIR_FROM_ROOT_ != '/') $_SERVER['REQUEST_URI'] = str_replace(_DIR_FROM_ROOT_,'',$_SERVER['REQUEST_URI']);

$url = explode('/',$_SERVER['REQUEST_URI']);
$item = $url['0'];

if (strstr($item, "*")) require('error.php');
if (strstr($item, ")")) require('error.php');
if (strstr($item, "(")) require('error.php');
if (strstr($item, "<")) require('error.php');
if (strstr($item, "$")) require('error.php');
if (strstr($item, ".")) require('error.php');
if (strstr($item, "@")) require('error.php');
if (strstr($item, "!")) require('error.php');
if (strstr($item, ">")) require('error.php');
if (strstr($item, "~")) require('error.php');
if (strstr($item, "-")) require('error.php');
if (strstr($item, ";")) require('error.php');
if (strstr($item, '"')) require('error.php');
if (strstr($item, "‘")) require('error.php');
if (strstr($item, "’")) require('error.php');
if (strstr($item, "`")) require('error.php');
if (strstr($item, "'")) require('error.php');
if (strstr($item, "¬")) require('error.php');
if (strstr($item, "؟")) require('error.php');
if (strstr($item, "?")) require('error.php');
unset($tmp);
foreach ($url as $item) {
    if (trim((string) $item)) {
        $tmp[] = str_replace('%20','_',trim((string) $item));
    }
}

if (!$tmp) {
    $tmp[0] = 'main';
}
$_GET['job'] = $tmp[0];
$_GET['all'] = $tmp;

if ($_POST) {
    $error['code'] = 403;
    if ($_SERVER["HTTP_REFERER"]) {
        //SERVER_NAME 127.0.0.1 / REQUEST_URI /sitefolder/
        if (strpos($_SERVER["HTTP_REFERER"],$_SERVER["SERVER_NAME"]) > 5 &&
            strpos($_SERVER["HTTP_REFERER"],$_SERVER["SERVER_NAME"]) < 9) {
            //ok
        } else {
            //var_dump($_SERVER);
            require('error.php');
        }
    } else {
        if ($_GET['job'] != 'api') {
            //post from no where ?
            $error['code'] = 'CD005';
            require('error.php');
        }

    }
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            // array is not allowed as a request ( not used inside the program )
            $error['code'] = 'AR002';
            require('error.php');
        }
        // trim none password values
        if ($key != 'password' && $key != 'new_password' && $key != 'old_password' && $key != 'confirm_password' && $key != 'data_file'
            && !strstr($key, '_link') && !strstr($key, '_text')) {
            $value = trim((string) $value);
        } else {
            // encode password values
            if ($value) $value = base64_encode($value);
        }
        // destroy executable codes , WUSIWYG style
        $value = str_replace("&", ' ', $value); // &amp;
        $value = str_replace("\\\\", '&#92;', $value);
        $value = str_replace("\\", '', $value);
        //$value = str_replace('/', '&frasl;', $value);
        $value = str_replace('<', '&lt;', $value);
        $value = str_replace('>', '&gt;', $value);
        $value = str_replace('"', ' ', $value); // &quot;
        $value = str_replace('‘', '&lsquo;', $value);
        $value = str_replace('’', '&rsquo;', $value);
        $value = str_replace("'", " ", $value); //&#39;
        $value = str_replace("%", "&#37;", $value);
        $value = str_replace("¬", '&not', $value);
        $value = str_replace("`", '', $value);
        // add more here
        $_POST[$key] = $value;
    }
}
if ($_SERVER['REQUEST_METHOD'] != 'GET' && $_SERVER['REQUEST_METHOD'] != 'POST' ) {
    $error['code'] = 'AR001';
    require 'error.php';
}
if ($_GET['job'] != 'ajax' || $_POST['todo'] != 'upload') {
    unset($_FILE);
    unset($_FILES);
}
unset($_REQUEST);
//unset($_SERVER);
// @TODO Clean SERVER vars
unset($_ENV);
//unset($GLOBALS);