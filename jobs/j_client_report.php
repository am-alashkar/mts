<?php
redirect_to_index();
if (!allowed('see_client_report') ) {
    redirect_to_index();
}
mark_active('client_report');
if (!data::$get->all[1]) {
    data::$get->page_title = 'كشف حساب زبون';
    $html = new style('client_report_mp');
    $sql = "WHERE group_id = 2 AND deleted IS NULL AND parent_id IS NULL;";
    if (allowed('super_admin')) $sql = "WHERE deleted IS NULL AND parent_id IS NULL;";
    $clients = db::$db->adv_select('*','members',$sql);
    foreach ($clients as $key => $client) {
        if ($client['disabled']) $clients->{$key}['ss'] = '<s>';
        if ($client['disabled']) $clients->{$key}['se'] = '</s>';
    }
    $html->fill_table('clients',$clients,true);
} else {
    if (!is_numeric(data::$get->all[1])) {
        create_error('Illegal Operation');
    }
    $user = member::load_member(data::$get->all[1],true);
    if ($user->is_empty()) {
        $html = style::msgbox('لا يمكن العثور على الحساب المطلوب','danger');
        $e = 1;
    } else if ($user->deleted) {
        $html = style::msgbox('يبدو أن الحساب المطلوب محذوف','danger');
        $e = 1;
    }
    if ($user->group_id == 2 && !allowed('super_admin'))
    {
        $html = style::msgbox('يبدو أن الحساب المطلوب ليس لزبون أو لا تملك الصلاحيات لمشاهدته','danger');
        $e = 1;
    }
    if (!$e) {
        data::$get->user_id = data::$get->all[1];
        data::$get->client_name = $user->name;
        if ($user->disabled) {
            data::$get->add_on_title = '(الحساب معطل)';
        }
        $html = new style('client_report_single');
        data::$get->page_title = 'كشف حساب '.$user->name;
    }

}
job::$body = $html;