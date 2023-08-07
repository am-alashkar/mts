<?php
if (!allowed('administration')) {
    create_error('Access Denied');
}
/**
 * $price_info[$sheet_name]['header'] = array()
 * $price_info[$sheet_name]['widths'] = array()
 * $price_info[$sheet_name]['rows'] = array(array())
 */
//$user_id
$user = member::load_member($user_id);
if ($user->deleted) create_error('User Deleted');
require './services/mobile_kazia/price.php';
//dumb($prices);
/** @var string $akey */
if (!$akey) $akey = 'رصيد جوال';
$price_info[$akey]['header'] = ['الاسم','النوع','السعر','ملاحظات','المبلغ'];
$price_info[$akey]['header1'] = ['@','@','@','@','@'];
$price_info[$akey]['widths'] = [30,30,20,30,20];
if ($prices['mtn_cash_type'] == 'true') {
    $type = 'مبلغ ثابت';
    $adder = $prices['mtn_cash_adder'];
} else {
    $type = '';
    $adder = '';
}
$price_info[$akey]['rows'][] = ['MTN كاش','كل 1000',+$prices['mtn_cash_price'],$type,$adder ];
if ($prices['syt_cash_type'] == 'true') {
    $type = 'مبلغ ثابت';
    $adder = $prices['syt_cash_adder'];
} else {
    $type = '';
    $adder = '';
}
$price_info[$akey]['rows'][] = ['Syriatel كاش','كل 1000',+$prices['syt_cash_price'],$type,$adder];
if ($prices['mtn_pos_type'] == 'true') {
    $type = 'مبلغ ثابت';
    $adder = $prices['mtn_pos_adder'];
} else {
    $type = '';
    $adder = '';
}
$price_info[$akey]['rows'][] = ['MTN لاحق الدفع','كل 1000',+$prices['mtn_pos_price'],$type,$adder];

if ($prices['syt_pos_type'] == 'true') {
    $type = 'مبلغ ثابت';
    $adder = $prices['syt_pos_adder'];
} else {
    $type = '';
    $adder = '';
}
$price_info[$akey]['rows'][] = ['Syriatel لاحق الدفع','كل 1000',+$prices['syt_pos_price'],$type,$adder];


if ($prices['syt_pre_type'] == 'true') {
    $type = 'مبلغ ثابت';
    $adder = $prices['syt_pre_adder'];
} else {
    $type = '';
    $adder = '';
}
$price_info[$akey]['rows'][] = ['Syriatel لاحق الدفع','كل 1000',+$prices['syt_pre_price'],$type,$adder];

if ($prices['mtn_pre_type'] == 'true') {
    $type = 'مبلغ ثابت';
    $adder = $prices['mtn_pre_adder'];
} else {
    $type = '';
    $adder = '';
}
$price_info[$akey]['rows'][] = ['MTN لاحق الدفع','كل 1000',+$prices['mtn_pre_price'],$type,$adder];