<?php
/**
 * Client ONLY KAZIA
 * New Order page
 * generate new order html
 * store it in $html
*/
if (!allowed('mobile_kazia_client')) {
    redirect_to_index();
}
data::$get->page_title = 'رصيد كازيات';
$user_id = member::$current->on_behalf ? member::$current->on_behalf : member::$current->id; // on behalf send ..
$main = new style('client_kazia');
$prices = null;
require './services/mobile_kazia/price.php';
$html = $main->get_part(1);
if (!$prices['mtn_cash_price']) $html->del_btn('mtn_cash_btn');
if (!$prices['mtn_pos_price']) $html->del_btn('mtn_pos_btn');
if (!$prices['mtn_pre_price']) $html->del_btn('mtn_pre_btn');

if (!$prices['syt_cash_price']) $html->del_btn('syt_cash_btn');
if (!$prices['syt_pos_price']) $html->del_btn('syt_pos_btn');
if (!$prices['syt_pre_price']) $html->del_btn('syt_pre_btn');

if (!$prices['wfa_cash_price']) $html->del_btn('wfa_cash_btn');
if (!$prices['wfa_pos_price']) $html->del_btn('wfa_pos_btn');
if (!$prices['wfa_pre_price']) $html->del_btn('wfa_pre_btn');


$html->fill($prices,null,true)->clear_unfilled(0);