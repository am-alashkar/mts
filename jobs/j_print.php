<?php
if (!member::check())redirect_to_index();
mark_active('view');
if (!data::$get->all[1])
{
    redirect_to_index();
}
data::$get->page_title = 'طباعة وصل صيانة';
$main = new style('maintenance_print');
$html = $main->get_part(3);
$bill = new maintenance(data::$get->all[1]);
if ($bill->hasError())
{
    create_error('In a Normal Universe Like OURS You Cannot View Something Does Not Exist','404');
}
$info = $bill->getFillInfo();
$main->part(1)->fill($info);

job::$body = $main;