<?php

if (member::$current->id < 1 ) {
    redirect_to_index();
}
$pass_score = 1; // minimum score
$pass_length = 3; // minimum length

data::$get->page_title = '{{change_password}}';
self::$body = new style('changepassword');

if (data::$get->save == 'changeme') {
    if (data::$get->confirm_password != data::$get->new_password) {
        $msg = '{{new_passwords_do_not_match}}' ;//.data::$vars->new_password.' === '.data::$vars->confirm_password;
    }
    $password = new password(data::$get->new_password);
    if (!$msg && $password->score() < $pass_score) $msg = '{{pass_too_week}}';
    if (!$msg && $password->length() < $pass_length) $msg = '{{pass_too_short}}';
    if (!$msg) {
        if (member::$current->change_pass(data::$get->old_password,data::$get->new_password)) {
            data::$get->lmscript = style::gritter('center','success','{{password_changed_success}}','{{success}}',0,true,false,true);
        } else {
            $msg = '{{old_password_is_not_correct}}';
        }
    }
}
if ($msg) {
    data::$get->lmscript = style::gritter('center','error',$msg,'{{error}}',0,true,false,true);
}
data::$get->lmscript .= '<script>
$("[href=\'<!-- CONFIG_home_link -->changepassword/\']").addClass("'.style::variable('active').'");
</script>';