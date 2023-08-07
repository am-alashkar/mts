<?php
if (!member::check()) die();
$fun = 'mdl_'.data::$get->type;
if (function_exists($fun)) $fun();
die();

function mdl_customer()
{
    $id = data::$get->id;
    if (!$id) die();
    $customer = db::$db->select('*','customers','id','=',$id)->first();
    if (!$customer) die();
    $html = new style('view_customer');
    $html->fill($customer)->create_modal('بيانات الزبون');
    print $html.'';
}