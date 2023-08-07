<?php
if (!member::check()) redirect_to_index();
mark_active('edit');
$html = new style('edit');
data::$get->page_title = 'تعديل صيانة';
$bill = new maintenance(data::$get->all[1]);
if ($bill->hasError())
{
    create_error('In a Normal Universe Like OURS You Cannot Edit Something Does Not Exist','404');
}
$bill->generateEditVars();

job::$body = $html;