<?php

if (!allowed('administration') ) {
    redirect_to_index();
}
switch (data::$get->save) {
    case 'add_new_user':
        add_new_user();
        break;
    case 'edit_user':
        edit_user();
        break;
    default:
        break;
}
$main = new style('admin_users');
$html = $main->get_part(1);
// TODO FIX THIS This is a raw mysqli .. for future edits you must edit this line if you want to use other database engines.
// TODO See db Class header for more info
$sql = 'SELECT mems.id , mems.name , mems.'.config::$get->login_by.' , mems.disabled , mems.info, grs.name `group` , grs.id `gid` FROM `'
    .db::$db->sfx.'members` `mems` LEFT JOIN `'.db::$db->pfx
    .'groups` `grs` on grs.id = mems.group_id WHERE mems.deleted is null AND mems.parent_id is null ;';
$users = db::$db->adv_select(null,null,$sql);
$btns = new style('admin_user_btns'); // extra buttons for each group each part has an order as user group id ( group 1 => part 1 )
foreach ($users as $key => $user) {
    if ($user['disabled']) {
        $users->$key['s_start'] = '<s>';
        $users->$key['s_end'] = '</s>';
    }
    $users->$key['disa'] = $user['disabled'];
    $users->$key['extra_btn'] = $btns->get_part($user['gid'])->fill($user).'';
    $users->$key['login_by'] = $user[config::$get->login_by];
}
$html->fill_table('users_table',$users,true);
//job::$body = $html;
job::$body = $html;
//data::$get->admincp_menu_open = 'menu-open';
//data::$get->lmscript .= '<script>
//$("[href=\'<!-- CONFIG_home_link -->admincp/users/\']").addClass("'.style::variable('active').'");
//</script>';
data::$get->page_title = '{{User_Accounts}}';

function add_new_user(){
    if (!data::$get->name || !data::$get->username || !data::$get->password || !data::$get->confirm_password
        || ! data::$get->group_id) {
        data::$get->msg = style::error_gritter('{{all_fields_required}}');
        return;
    }
    // check if password == confirm_password
    if (data::$get->password != data::$get->confirm_password)
    {
        data::$get->msg[] = '{{passwords_do_not_match}}';
    }
    // check for username if exists
    $tmp = db::$db->select('id','members',config::$get->login_by,'=',data::$get->username)->first();
    if ($tmp) {
        data::$get->msg[] = '{{login_used_before}}';
    }
    // check for name if duplicate ??
    $tmp = db::$db->select('id','members','name','=',data::$get->name)->first();
    if ($tmp) {
        data::$get->msg[] = '{{name_used_before}}';
    }
    // check for weak password
    $pass = new password(data::$get->password);
    if ($pass->length() < 8) {
        data::$get->msg[] = '{{pass_too_short}}';
    }
    if ($pass->score() < 3) {
        data::$get->msg[] = '{{pass_too_week}}';
    }
    // check if group is OK
    $tmp = db::$db->select('selectable','groups','id','=',data::$get->group_id)->first()['selectable'];
    if (!$tmp || !data::$get->group_id) {
        create_error('Any thing can happen');
    }
    if (!data::$get->msg) {
        // OK
        $in[config::$get->login_by] = data::$get->username;
        $in['name'] = data::$get->name;
        $in['password'] = $pass->get_hash();
        $in['group_id'] = data::$get->group_id;
        if (db::$db->insert('members',$in)) {
            data::$get->msg = style::big_success_gritter('{{please_wait}}','{{user_added_successfully}}').refresh();
        } else {
            create_error('Something went wrong ! Please contact website administrator ASAP!');
        }
    } else {
        $msg = implode('<br>',data::$get->msg);
        data::$get->msg = style::gritter('center','error','',$msg,0,true,false);
    }
    //
}
function edit_user() {
    if (!data::$get->name || !data::$get->username || !data::$get->group_id) {
//        var_dump(data::$get);
//        die();
        data::$get->msg = style::error_gritter('{{all_fields_required}}');
        return;
    }
    $user = member::load_member(data::$get->id);
    if ($user->deleted || $user->disabled) {
        data::$get->msg = style::error_gritter($user->message);
        return;
    }
    // check for username if exists
    $tmp = db::$db->select('id','members',config::$get->login_by,'=',data::$get->username)->first();
    if ($tmp && $tmp['id'] != data::$get->id) {
        data::$get->msg[] = '{{login_used_before}}';
    }
    // check for name if duplicate ??
    $tmp = db::$db->select('id','members','name','=',data::$get->name)->first();
    if ($tmp && $tmp['id'] != data::$get->id) {
        data::$get->msg[] = '{{name_used_before}}';
    }
    // check if group is OK
    if (data::$get->group_id != 'NoChange') {
        $tmp = db::$db->select('selectable','groups','id','=',data::$get->group_id)->first();
        if (!$tmp || !data::$get->group_id) {
            create_error('Any thing can happen Error has occurred.');
        }
        $in['group_id'] = data::$get->group_id;
    }

    if (!data::$get->msg) {
        // OK
        $in[config::$get->login_by] = data::$get->username;
        $in['name'] = data::$get->name;

        if (db::$db->update('members',$in,'id','=',data::$get->id)) {
            data::$get->msg = style::big_success_gritter('{{please_wait}}','{{user_edited_successfully}}').refresh();
        } else {
            create_error('Something went wrong ! Please contact website administrator ASAP!');
        }
    } else {
        $msg = implode('<br>',data::$get->msg);
        data::$get->msg = style::gritter('center','error','',$msg,0,true,false);
    }
}