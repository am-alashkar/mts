<?php
if (!member::check()) create_error('Not Allowed');
$bill = new maintenance(data::$get->id);
if ($bill->hasError()) create_error($bill->getError());
if ($bill->stat == 3) {
    //die(refresh());
}
if (data::$get->new_report == '')
{
    create_error('يجب كتابة تقرير');
}
$report = new single_log();
$report->buildFromInput();
$report->prev_stat = $bill->stat;
if ($bill->stat == 3)
{
    $report->prev_stat = $report->new_stat = 3;
}
if (!$report->insert($bill->stat == 3))
{
    create_error(db::$db->get_last_error());
}
refresh(true);
print style::big_success_gritter('الرجاء الانتظار','تمت العملية بنجاح');