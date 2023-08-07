<?php
if (!member::check()) redirect_to_index();
$tmp = db::$db->select('*','maintenance','id','=',data::$get->id)->first();
if (!$tmp)
{
    print style::error_gritter('الطلب غير موجود');
    die();
}
if ($tmp['stat'] == '3')
{
    print style::error_gritter('الطلب تم تسليمه مسبقاً');
    die();
}
if (!data::$get->confirm)
{
    $html = new style('deliver');
    $html->create_modal('تسليم للزبون');
    print $html;
    $html = new style('maintenance_view');
    $btn = $html->get_btn('deliver_to_customer_btn');
    $btn->fill(data::$get);
    print $btn;
    die();
}
$result = new result();
$result->stat = 3; // delivered
$result->out_by = member::get_id();
$out_date = '';
try
{
    $out_date = new DateTime(data::$get->out_date);
} catch (Exception $exception)
{
    $out_date = new DateTime();
}
$result->out_date = $out_date->format(config::$get->storedatetime);
$result->out_notes = data::$get->notes;
if (db::$db->update('maintenance',$result,'id','=',data::$get->id))
{
    print style::big_success_gritter('الرجاء الانتظار', 'تمت العملية بنجاح');
    refresh(true);
} else
{
    print style::big_error_gritter(db::$db->get_last_error(),'حدث خطأ ');
}
die();