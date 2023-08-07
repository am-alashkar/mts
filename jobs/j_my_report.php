<?php
redirect_to_index();
if (!allowed('can_send_bills')) {
    redirect_to_index();
}
mark_active('my_report');
$user_id = member::get_family_id();
$user = member::load_member($user_id,true);
if ($user->is_empty()) {
    $html = style::msgbox('لا يمكن العثور على الحساب المطلوب','danger');
    $e = 1;
} else if ($user->deleted) {
    $html = style::msgbox('يبدو أن الحساب المطلوب محذوف','danger');
    $e = 1;
}
if (!$e) {
    data::$get->user_id = $user_id;
    data::$get->client_name = $user->name;
    if ($user->disabled) {
        data::$get->add_on_title = '(الحساب معطل)';
    }
    $html = new style('client_report_single');
    data::$get->page_title = 'كشف حساب '.$user->name;
}
job::$body = $html;


