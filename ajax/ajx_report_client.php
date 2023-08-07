<?php
if (!allowed('see_client_report') && !allowed('can_send_bills')) {
    redirect_to_index();
}

/** object(data)#2 (15) {
 * ["now"]=> string(19) "2022-03-16 01:48:57"
 * ["job"]=> string(4) "ajax"
 * ["all"]=> array(1) { [0]=> string(4) "ajax" }
 * ["lmscript"]=> NULL
 * ["page_title"]=> NULL
 * ["hook"]=> NULL
 * ["timezone"]=> string(13) "Asia/Damascus"
 * ["todo"]=> string(13) "report_client"
 * ["id"]=> string(0) "4"
 * ["df_d"]=> string(2) "01"
 * ["df_m"]=> string(2) "03"
 * ["df_y"]=> string(4) "2022"
 * ["dt_d"]=> string(2) "16"
 * ["dt_m"]=> string(2) "03"
 * ["dt_y"]=> string(4) "2022"
 * }
 */
$months = ['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Nov','11'=>'Oct','12'=>'Dec'];
if (!allowed('see_client_report')) data::$get->id = member::get_family_id();
$user = member::load_member(data::$get->id,true);
if ($user->is_empty() || $user->deleted) {
    print style::big_error_gritter('لا يمكن العثور على هذا المستخدم','حدث خطأ');
    die();
}
$df = data::$get->df_y.'-'.$months[+(int)data::$get->df_m].'-'.data::$get->df_d;
$dt = data::$get->dt_y.'-'.$months[+(int)data::$get->dt_m].'-'.data::$get->dt_d;
if ($df == 'undefined--' || $df == '--') $df = 'now';
if ($dt == 'undefined--' || $dt == '--') $dt = 'now';
try {
    $date_from = new DateTime($df);
    $date_from->setTime(0,0,0);
    if ($date_from->format('Y-M-d') != $df && $df != 'now') {
        throw new Exception('Date Error');
    }
    if ($df == 'now') {
        $date_from->setDate($date_from->format('Y'),$date_from->format('m'),1);
    }
} catch (Exception $e) {
    print $e->getMessage();
    print '<script>
$("#date_from").addClass("is-invalid");
</script>';
    $err = 1;
}
try {
    $date_to = new DateTime($dt);
    $date_to->setTime(23,59,59);
    if ($date_to->format('Y-M-d') != $dt && $dt != 'now') {
        throw new Exception('Date Error');
    }
} catch (Exception $e) {
    print '<script>
$("#date_to").addClass("is-invalid");
</script>';
    $err = 1;
}
if ($err) {
    var_dump($dt);
    print style::error_gritter('التاريخ غير صحيح');
    die();
}
// prev balance
$input['user_id'] = $user->id;
$input['date_from'] = $date_from->format(config::$get->storedatetime);
$input['date_to'] = $date_to->format(config::$get->storedatetime);
$input['operation'] = 'report_client';
$sql = "WHERE user_id = ".$user->id." AND ba = 1 AND send_date < '".$date_from->format(config::$get->storedatetime)."' ;";
$prev = +db::$db->adv_select('SUM(price) as pb','bills',$sql)->first()['pb'];
$info['date_from'] = $date_from->format(config::$get->storedatetime);
$info['pre_balance'] = -$prev;
$info['date_to'] = $date_to->format(config::$get->storedatetime);
$info['username'] = $user->name;
// operations
$sql = "WHERE user_id = ".$user->id." AND ba = 1 AND send_date >= '".$date_from->format(config::$get->storedatetime)."' AND send_date <= '".$date_to->format(config::$get->storedatetime)."';";
$ops = db::$db->adv_select('*','bills',$sql);
$bills = bill::get_fillables($ops,true,-$prev);
$info['after_balance'] = +$bills->last()['info']['balance'];
if (!data::$get->is_export) {
    $html = new style('client_report_ajx');
    $html->fill_table('done_table',$bills,true,'info','لا يوجد')->fill($info);
    data::$get->exp_data = data::encode($input);
    print $html;
    die();
}
data::$get->export_data['info'] = $info;
data::$get->export_data['bills'] = $bills;
// current bal