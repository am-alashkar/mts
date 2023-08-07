<?php
$options = array (
    'name' => _COOKIE_NAME_.'_UBX',
    'cookie_lifetime' =>  60*60*24*2, // 2 days
    'cookie_path' => '/', // path to use in case of conflicts but preferred not to use
    //'use_only_cookies' => true,
    'use_strict_mode' => true, // Applications are protected from session fixation via session adoption with strict mode
    'sid_length' => 100, // preferred for security max 200 (using more than 200 will not work on some browsers)
    //'cookie_secure' => _FORCE_SSL_,     // send over https
    //'httponly' => false,    // If true will marks the cookie as accessible only through the HTTP protocol.
    // This means that the cookie won't be accessible by scripting languages, such as JavaScript.
    // This setting can effectively help to reduce identity theft through XSS attacks (although it is not supported by all browsers).
    'cookie_samesite' => 'Strict', // None || Lax // no third party on post  || Strict // no third party on get and post
    'SameSite' => 'Strict' // None || Lax  || Strict // no third party
);
if ($_GET['job'] != 'api') {
    session_start($options);
    if (!$_SESSION['create_time']) {
        $_SESSION['user_id'] = 0;
        $_SESSION['server_info'] = $tmp;
        $_SESSION['create_time'] = date('Y-M-d h:i:s');
        //$_SESSION['lang'] = _DEFAULT_LANG_;
        $_SESSION['timezone'] = _DEFAULT_TIMEZONE_;
    }
//$_SESSION['lang'] = _DEFAULT_LANG_;
//    $tmp = $_SERVER['HTTP_USER_AGENT'].$_SERVER['HTTP_ACCEPT'].$_SERVER['HTTP_ACCEPT_LANGUAGE']
//        .$_SERVER['HTTP_ACCEPT_ENCODING'];
//    $tmp = base64_encode($tmp);
//    if ($_SESSION['server_info'] != $tmp && $_GET['job'] != 'hook' && $_GET['job'] != 'ajax') {
//        $_SESSION['user_id'] = 0;
//        $_SESSION['server_info'] = $tmp;
//        $_SESSION['create_time'] = date('Y-M-d h:i:s');
//        //$_SESSION['lang'] = _DEFAULT_LANG_;
//        $_SESSION['timezone'] = _DEFAULT_TIMEZONE_;
//        //session_commit();
//    }
}


