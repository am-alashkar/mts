<?php
redirect_to_index();
if(!allowed('can_send_bills')) redirect_to_index();

mark_active('my_bills');
data::$get->page_title = 'طلباتي';
/** His own bills */
$user_id = member::get_family_id();
$sql = "WHERE user_id = '".$user_id."' AND ( done IS NULL OR done = 0 ) ORDER BY id DESC LIMIT 100;";
$undone = db::$db->adv_select('*','bills',$sql);
$undone = bill::get_fillables($undone);
$sql = "WHERE user_id = '".$user_id."' AND done = 1 ORDER BY done_date DESC LIMIT 20;";
$done = db::$db->adv_select('*','bills',$sql);
$done = bill::get_fillables($done);
$main = new style('my_bills_main');
$main->fill_table('undone_table',$undone,false,'info','لا يوجد')
->fill_table('done_table',$done,false,'info','لا يوجد');
job::$body = $main;