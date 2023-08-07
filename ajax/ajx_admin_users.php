<?php

if (!allowed('administration')) {
    redirect_to_index();
}
switch (data::$get->btn) {
    case 'add':
        add_new_user();
        break;
    case 'edit':
        // Edit any thing but password .
        edit_user();
        break;
    case 'disable':
        disable_user();
        break;
    case 'reset_password':
        // edit only password
        reset_password();
        break;
    case 'delete':
        // delete a user
        delete_user();
        break;
    case 'permission':
        // edit permissions
        admin_user_permission();
        break;
    case 'prices':
        // edit prices
        edit_user_prices();
        break;
    default:
        print style::error_gritter('Operation Not found : '.data::$get->btn);
}
function edit_user_prices() {
    $main = new style('admin_users');
    $html = $main->get_part(10);
    $user = member::load_member(data::$get->id,1);
    if ($user->is_empty()) {
        print style::big_error_gritter($user->message,'{{user_not_found}}');
        die();
    }
    if ($user->deleted) {
        print style::big_error_gritter($user->message,'{{can_not_edit_member}}');
        die();
    }
    if ($user->parent_id) {
        print style::big_error_gritter($user->message,'{{can_not_edit_member}}');
        die();
    }
    //var_dump($user);
    if (data::$get->confirm) {
        db::$db->update('members',['price_group'=>data::$get->v],'id','=',data::$get->id);
        print style::success_gritter('تم تعديل فئة الأسعار');
        die();
    }
    $cprice = db::$db->select('*','custom_prices','user_id','=',$user->id);
    foreach ($cprice as $key => $item) {
        foreach (phrases::$price_tags as $k => $v) {
            if ($item['script'] == $v)
            {
                $cprice->{$key}['name'] =$k;
                break;
            }
        }

    }
    $html->fill_table('custom_price_table',$cprice,false);
    $html->fill($user)->create_modal('تعديل أسعار الزبون');
    print $html;
    die();
}
function add_new_user(){
    $main = new style('admin_users');
    $btn = $main->get_btn('add_new_user_btn');
    print $btn;
    $html = $main->get_part(2)->create_modal('{{add_new_user}}');
    $groups = db::$db->select('*','groups','selectable','=','1');
    $options = '';
    foreach ($groups as $group) {
        $options .= '<option value="'.$group['id'].'">'.$group['name'].'</option>';
    }
    $html->str_replace('<!-- GROUPS -->',$options);
    print $html;
}
function edit_user() {
    $main = new style('admin_users');
    $user = member::load_member(data::$get->id);
    if ($user->is_empty()) {
        print style::big_error_gritter($user->message,'{{user_not_found}}');
        die();
    }
    if ($user->deleted || $user->disabled) {
        print style::big_error_gritter($user->message,'{{can_not_edit_member}}');
        die();
    }
    $groups = db::$db->select('*','groups','selectable','=','1');
    $options = '';
    $found = false;
    foreach ($groups as $group) {
        if ($group['id'] == $user->group_id) {
            $options .= '<option value="'.$group['id'].'" selected>'.$group['name'].'</option>';
            $found = true;
        } else {
            $options .= '<option value="'.$group['id'].'">'.$group['name'].'</option>';
        }
    }
    $user->login_by = $user->{config::$get->login_by};
    if (!$found) {
        $options = '<option value="NoChange" selected >'.$user->group.' </option>';//.$options;
    }
    $html = $main->get_part(3)->fill($user)->create_modal('{{edit_user}} <b>'.$user->name.'</b>');
    $html->str_replace('<!-- GROUPS -->',$options);
    print $html;
}
function reset_password() {
    $user = member::load_member(data::$get->id);
    if ($user->is_empty()) {
        print style::big_error_gritter($user->message,'{{user_not_found}}');
        die();
    }
    if ($user->deleted) {
        print style::big_error_gritter($user->message,'{{can_not_edit_member}}');
        die();
    }
    if (data::$get->confirm) {
        $password = new password(data::$get->np);
        if ($password->score() < 3 || $password->length() < 8) {
            create_error('Fatal Error 75 , Please refresh your page.');
        }
        if (data::$get->id < 2) create_error('Security Error. Operation Halted'.data::$get->id);
        if (db::$db->update('members',['password'=>$password->get_hash()],'id','=',data::$get->id))
        {
            print style::big_success_gritter('{{password_changed_success}}');
        } else
        {
            print style::big_error_gritter('{{please_contact_website_admin}}','{{error_while_doing_operation}}');
        }
        die();
    }
    $main = new style('admin_users');
    $checkbox = style::check_box('pass_reset_confirm','{{confirm}} {{reset_password}}');
    $user->add('confirm_checkbox',$checkbox['checkbox']);
    $user->add('confirm_label',$checkbox['label']);
    $user->new_password = get_a_password();
    $user->new_password_b64 = base64_encode($user->new_password);
    $html = $main->get_part(4)->fill($user)->create_modal('{{Reset_Password}} : <b>'.$user->name.'</b>');
    print $html;
}

function get_a_password() {
    return password::get_random_password();
}
function delete_user() {
    $user = member::load_member(data::$get->id);
    if ($user->is_empty()) {
        print style::big_error_gritter($user->message,'{{user_not_found}}');
        die();
    }
    if ($user->deleted) {
        print style::big_error_gritter($user->message,'{{can_not_edit_member}}');
        die();
    }
    if (data::$get->confirm) {
        if (db::$db->update('members',['password'=>'','deleted'=>member::$current->id],'id','=',data::$get->id))
        {
            print style::big_success_gritter('{{please_wait}}','{{user_delete_success}}').refresh();
        } else
        {
            print style::big_error_gritter('{{please_contact_website_admin}}','{{error_while_doing_operation}}');
        }
        die();
    }
    $main = new style('admin_users');
    $checkbox = style::check_box('user_delete_confirm','{{confirm}} {{delete_user}}');
    $user->add('confirm_checkbox',$checkbox['checkbox']);
    $user->add('confirm_label',$checkbox['label']);
    $html = $main->get_part(5)->fill($user)->create_modal('{{delete_user}} : <b>'.$user->name.'</b>');
    print $html;
}
function disable_user() {
    $user = member::load_member(data::$get->id,true);
    if ($user->is_empty()) {
        print style::big_error_gritter($user->message,'{{user_not_found}}');
        die();
    }
    if ($user->deleted) {
        print style::big_error_gritter($user->message,'{{can_not_edit_member}}');
        die();
    }
    if (data::$get->confirm) {
        $up['disable_note'] = data::$get->dis_note;
        if (data::$get->disable == '2') {
            $up['disabled'] = null;
            $msg = '{{user_activate_success}}';
        } else {
            $up['disabled'] = '1';
            $msg = '{{user_disable_success}}';
        }
        if (db::$db->update('members',$up,'id','=',data::$get->id))
        {
            print style::big_success_gritter('{{please_wait}}',$msg).refresh();
        } else
        {
            print style::big_error_gritter('{{please_contact_website_admin}}','{{error_while_doing_operation}}');
        }
        die();
    }
    $main = new style('admin_users');
    $checkbox = style::check_box('user_disabled','{{disable_user}}','',$user->disabled == 1);
    $user->add('confirm_checkbox',$checkbox['checkbox']);
    $user->add('confirm_label',$checkbox['label']);
    $html = $main->get_part(6)->fill($user)->create_modal('{{disable_enable_user}} : <b>'.$user->name.'</b>');
    print $html;
    die();
}
function admin_user_permission() {
    $user = member::load_member(data::$get->id,true);
    if ($user->is_empty()) {
        print style::big_error_gritter($user->message,'{{user_not_found}}');
        die();
    }
    if ($user->deleted) {
        print style::big_error_gritter($user->message,'{{can_not_edit_member}}');
        die();
    }
    if ($user->disabled) {
        print style::big_error_gritter($user->message,'لا يسمح بتعديل صلاحيات مستخدم تم تعطيله');
        die();
    }
    $perList = member::get_permission_list(data::$get->id);
    if ($perList->is_empty()) {
        print style::big_error_gritter('{{this_user_has_no_permissions_to_edit}}');
        die();
    }
    if (data::$get->confirm) {
        if (is_numeric(data::$get->max_credit))
        {
            db::$db->update('members',['credit'=>data::$get->max_credit],'id','=',data::$get->id);
        }
        db::$db->delete('permissions','user_id','=',data::$get->id);
        $in['user_id'] = data::$get->id;
        foreach ($perList as $item) {
            foreach ($item as $val) {
                if (data::$get->{$val['script']} == 'true') {
                    $in['script'] = $val['script'];
                    $in['needs'] = $val['needs'];
                    $in['only_group'] = $user->group_id;
                    db::$db->insert('permissions',$in);
                    if (is_array($val['sub'])) {
                        foreach ($val['sub'] as $sub_item) {
                            if (data::$get->{$sub_item['script']} == 'true')
                            {
                                $in['script'] = $sub_item['script'];
                                $in['needs'] = $sub_item['needs'];
                                $in['only_group'] = $user->group_id;
                                db::$db->insert('permissions',$in);
                            }

                        }
                    }
                }
            }
        }
        print style::big_success_gritter('{{operation_success}}');
        die();
    }
    $main = new style('admin_users');
    $result_html = '';
    foreach ($perList as $key=>$val) {
        unset($checkList);
        $html = $main->get_part(8);
        foreach ($val as $item) {
            $check['extra'] = '';
            $item['group_name'] = '{{'.$key.'}}';
            $item['script'] = trim($item['script'].'');
            $check = style::check_box($item['script'],$item['name'],'permissions',$item['granted']);
            if (is_array($item['sub'])) {
                foreach ($item['sub'] as $sub_item) {
                    $check['extra'] .= $main->get_part(9)->fill(style::check_box($sub_item['script'],$sub_item['name'],'permissions',$sub_item['granted']));
                }
            }
            $checkList[] = $check;
        }
        $result_html .= $html->fill_template($checkList)->fill($item).'';
    }
    $main->part(7)
        ->str_replace('<!-- PERS_HTML -->',$result_html)
        ->fill($user)
        ->create_modal('{{permissions}} : <b>'.$user->name.'</b>');
    print $main;
}