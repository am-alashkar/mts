<?php
if ($_SESSION['lang'] && in_array($_SESSION['lang'], _LANG_LIST_) ) {
    $lang = $_SESSION['lang'];
} else {
    $lang = substr((string) $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
//$acceptLang = ['fr', 'it', 'en'];
    $lang = in_array($lang, _LANG_LIST_) ? $lang : _DEFAULT_LANG_;
    $_SESSION['lang'] = $lang;
    //$_SESSION['auto_lang'] = true;
    // @TODO : auto lang if logged in will change the lang to user's ?
    // or will change only if user did not use explicable language change
}
define('_LANG_',$lang);
//die($_SESSION['lang']);
$timezone = _DEFAULT_TIMEZONE_;
if ($_SESSION['timezone']) $timezone = $_SESSION['timezone'];
date_default_timezone_set($timezone);
