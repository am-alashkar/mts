<?php
if (member::$current->id > 0) {
    redirect_to_index();
}
if (data::$get->login) {
    if (data::$get->username == '' || data::$get->password == '') {
        data::$get->msg = '{{all_fields_required}}';
    } else {
        $user = member::load_member_by_login(data::$get->username);
        if (!$user) {
            $login_error = true;
        } else {
            $up['last_try'] = time();
            db::$db->update('members',$up,'id','=',$user['id']);
            if (time() - $user['last_try'] < 5) {
                $login_error = true;
            } else
            if (!check_pass($user['password'],data::$get->password)) {
                $login_error = true;
            } else {
                $login_done = member::login($user['id']);
            }
        }
    }
}
if ($login_error) {
    data::$get->msg = '{{login_error}}';//.data::$get->password;
}
//var_dump($login_done);
if ($login_done) {
    // @TODO check redirect to previous page
    redirect_to_prev();
}
$html = new style('login_page');
job::$body = $html;
job::$top = '';
job::$bottom = '';
data::$get->page_title = '{{login}}';
data::$get->lmscript = '<script>
$("a[href=\''.config::$get->home_link.'login/\']").addClass("'.style::variable('active').'");
</script>';
function check_pass($user_pass,$input_pass) {
    $p = new password($input_pass);
    return $p->check($user_pass);
}