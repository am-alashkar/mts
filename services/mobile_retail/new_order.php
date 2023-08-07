<?php
/**
 * Client ONLY
 * New Order page
 * generate new order html
 * store it in $html
*/
if (!allowed('mobile_retail_client')) {
    redirect_to_index();
}
data::$get->page_title = 'رصيد جوال';
$user_id = member::$current->on_behalf ? member::$current->on_behalf : member::$current->id; // on behalf send ..
$main = new style('client_mobile');
$prices = null;
require './services/mobile_retail/price.php';
$html = $main->get_part(1);
if (!$prices['mtn_cash_price']) $html->del_btn('mtn_cash_btn');
if (!$prices['mtn_pos_price']) $html->del_btn('mtn_pos_btn');
foreach ($prices['mtn_prepaid'] as $isx=>$price) {
    if (!$price['price']) unset($prices['mtn_prepaid'][$isx]);
}
if (!$prices['mtn_prepaid'] || count($prices['mtn_prepaid']) < 1) $html->del_btn('mtn_pre_btn');
if (!$prices['syt_cash_price']) $html->del_btn('syt_cash_btn');
if (!$prices['syt_pos_price']) $html->del_btn('syt_pos_btn');
foreach ($prices['syt_prepaid'] as $isx=>$price) {
    if (!$price['price']) unset($prices['syt_prepaid'][$isx]);
}
if (!$prices['syt_prepaid'] || count($prices['syt_prepaid']) < 1) $html->del_btn('syt_pre_btn');
if (!$prices['wfa_cash_price']) $html->del_btn('wfa_cash_btn');
if (!$prices['wfa_pos_price']) $html->del_btn('wfa_pos_btn');
foreach ($prices['wfa_prepaid'] as $isx=>$price) {
    if (!$price['price']) unset($prices['wfa_prepaid'][$isx]);
}
if (!$prices['wfa_prepaid'] || count($prices['wfa_prepaid']) < 1) $html->del_btn('wfa_pre_btn');
$html->fill_table('mtn_pre_table',$prices['mtn_prepaid'],false)
    ->fill_table('syt_pre_table',$prices['syt_prepaid'],false)
    ->fill_table('wfa_pre_table',$prices['wfa_prepaid'],false)
    ->fill($prices,null,true);