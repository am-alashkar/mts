<?php
if (!allowed('administration')) redirect_to_index();
switch (data::$get->btn)
{
    case 'delete_c_price':
        delete_c_price();
        break;
    default:
        create_error('أمر خاطئ');
}

function delete_c_price() {
    $user = data::$get->uid;
    $script = data::$get->scr;
    if (!$user || !is_numeric($user) || !$script) create_error('البيانات غير صحيحة');
    $sql = "WHERE user_id = '".$user."' AND script = '".$script."' ;";
    $tmp = db::$db->delete('custom_prices','','','',$sql.'');
    print style::success_gritter('تم الحذف',false);
    die();
}