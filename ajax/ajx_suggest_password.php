<?php
if (member::$current->id < 1) {
    die('Please Login First');
}
$pass  = password::get_random_password();
print $pass.'<input type="hidden" id="suggested_password_b64" value="'.base64_encode($pass).'">
<input type="hidden" id="suggested_password_clipboard" value="'.$pass.'">
';
die();