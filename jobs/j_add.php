<?php
if (!member::check()) redirect_to_index();
mark_active('add');
$html = new style('add');
data::$get->page_title = 'إضافة صيانة';
$bill = new maintenance(data::$get->all[1]);
if (!$bill->hasError())
{
    //create_error('In a Normal Universe Like OURS You Cannot Edit Something Does Not Exist','404');
    $bill->generateEditVars();
}

job::$body = $html;