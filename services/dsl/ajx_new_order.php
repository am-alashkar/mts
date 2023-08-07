<?php
/**
 * Client ONLY KAZIA
 *  New order create and store in DB
 */

if (!allowed('mobile_kazia_client')) redirect_to_index();

$html = new style('client_kazia');
$user_id = member::$current->id;
$prices = null;
require './services/mobile_kazia/price.php';
if (!$prices) {
    print style::error_gritter('حدث خطأ الرجاء تحديث الصفحة');
    die();
}
// load btn html and print it
print $html->get_btn(data::$get->op.'_send_btn');
// op : mtn_cash mtn_pos mtn_pre syt_cash ...
$price_f = data::$get->op.'_price_calc';
if (function_exists($price_f)) $price = $price_f($prices);
else create_error('Price Function not found');
if (!is_numeric(data::$get->amount))  create_error('المبلغ غير صحيح');
$company = substr(data::$get->op,0,3);
if (!is_numeric(data::$get->phone)) {
    if ( strtolower($company) != 'syt' ) create_error('الكود غير صحيح');
    else create_error('الرقم غير صحيح');
}
if (!is_numeric(data::$get->code) && strtolower($company) != 'syt' ) create_error('الكود غير صحيح');
$cities = $prices[$company.'_cits'];
if (is_array($cities))
{
    if (!in_array(data::$get->city,$cities)) {
        create_error('يجب اختيار مدينة');
    }
} else data::$get->city = '';


$bill = new bill();
$bill->script = 'mobile_kazia';
$bill->sub_script = data::$get->op;
$bill->user_id = $user_id;
$bill->price = $price;
$bill->sender_id = $user_id;
$bill->amount = data::$get->amount;
$bill->phone = data::$get->phone;
$bill->balance();
if (!$bill->is_ok()) {
    print style::error_gritter('رصيدك غير كافي لاتمام العملية');
    die();
}
$bill->ba = 1;
$f_info = data::$get->op.'_builder';
if (function_exists($f_info)) $all = $f_info($bill);
else create_error('Builder Function Not found');

$bill->info = $all['info'];
//$bill->html = $all['html'];
$bill->history = $all['history'];
$id = $bill->insert();
if ($id) {
    print style::big_success_gritter('رقم العملية '.$id,'تمت العملية بنجاح');
    print '<script>
clear_all();
</script>';
} else {
    print style::big_error_gritter('حدث خطأ ، لا يمكن المتابعة');
    die();
    //print db::$db->get_last_error();

}
function syt_pos_price_calc($prices) {
    $price = $prices['syt_pos_price'];
    print "p".$price;
    $adder = $prices['syt_pos_type'];
    $add_am = $prices['syt_pos_adder'];
    $amount = data::$get->amount;
    $total = 0;
    if ($amount > 0)
    {
        $total = ceil($amount * $price / 1000);
        if ($adder) $total += $add_am;
        $total = ceil($total);
    }
    return $total;
}
function syt_pre_price_calc($prices) {
    $price = $prices['syt_pre_price'];
    $adder = $prices['syt_pre_type'];
    $add_am = $prices['syt_pre_adder'];
    $amount = data::$get->amount;
    $total = 0;
    if ($amount > 0)
    {
        $total = ceil($amount * $price / 1000);
        if ($adder) $total += $add_am;
        $total = ceil($total);
    }
    return $total;
}
function syt_cash_price_calc($prices) {
    $price = $prices['syt_cash_price'];
    $adder = $prices['syt_cash_type'];
    $add_am = $prices['syt_cash_adder'];
    $amount = data::$get->amount;
    $total = 0;
    if ($amount > 0)
    {
        $total = ceil($amount * $price / 1000);
        if ($adder) $total += $add_am;
        $total = ceil($total);
    }
    return $total;
}
function mtn_pos_price_calc($prices) {
    $price = $prices['mtn_pos_price'];
    $adder = $prices['mtn_pos_type'];
    $add_am = $prices['mtn_pos_adder'];
    $amount = data::$get->amount;
    $total = 0;
    if ($amount > 0)
    {
        $total = ceil($amount * $price / 1000);
        if ($adder) $total += $add_am;
        $total = ceil($total);
    }
    return $total;
}
function mtn_pre_price_calc($prices) {
    $price = $prices['mtn_pre_price'];
    $adder = $prices['mtn_pre_type'];
    $add_am = $prices['mtn_pre_adder'];
    $amount = data::$get->amount;
    $total = 0;
    if ($amount > 0)
    {
        $total = ceil($amount * $price / 1000);
        if ($adder) $total += $add_am;
        $total = ceil($total);
    }
    return $total;
}
function mtn_cash_price_calc($prices) {
    $price = $prices['mtn_cash_price'];
    $adder = $prices['mtn_cash_type'];
    $add_am = $prices['mtn_cash_adder'];
    $amount = data::$get->amount;
    $total = 0;
    if ($amount > 0)
    {
        $total = ceil($amount * $price / 1000);
        if ($adder) $total += $add_am;
        $total = ceil($total);
    }
    return $total;
}

/**
 * @param bill $bill
 */
function mtn_pos_builder($bill) {
    $all['info'] = $bill->to_array();
    $all['info']['type_html'] = 'جملة MTN لاحق الدفع';
    $all['info']['company'] = 'MTN';
    $all['info']['price'] = $bill->price;
    $all['info']['infos'] = 'الكمية : '.$bill->amount.'<br>الرقم : '.$bill->phone .'<br>الكود : '.data::$get->code;
    $field['name'] = 'الشركة';
    $field['value'] = 'MTN';
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المبلغ';
    $field['value'] = $bill->amount;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'رقم الهاتف';
    $field['value'] = $bill->phone;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الكود';
    $field['value'] = data::$get->code;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المدينة';
    $field['value'] = data::$get->city;
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الاسم';
    $field['value'] =data::$get->name;
    $field['copy_btn'] = false;
    $field['hide_from_staff'] = true;
    $all['info']['fields'][] = $field;
    $history['type'] = 'new';
    $history['date'] = date(config::$get->storedatetime);
    $all['history'][] = $history;
    return $all;
}

/**
 * @param bill $bill
 */
function syt_pos_builder($bill) {
    $all['info'] = $bill->to_array();
    $all['info']['type_html'] = 'جملة SYRIATEL لاحق الدفع';
    $all['info']['company'] = 'SYRIATEL';
    $all['info']['price'] = $bill->price;
    $all['info']['infos'] = 'المبلغ : '.$bill->amount.'<br>الكود : '.$bill->phone;
    $field['name'] = 'الشركة';
    $field['value'] = 'SYRIATEL';
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المبلغ';
    $field['value'] = $bill->amount;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الكود';
    $field['value'] = $bill->phone;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المدينة';
    $field['value'] = data::$get->city;
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الاسم';
    $field['value'] = data::$get->name;
    $field['copy_btn'] = false;
    $field['hide_from_staff'] = true;
    $all['info']['fields'][] = $field;
    $history['type'] = 'new';
    $history['date'] = date(config::$get->storedatetime);
    $all['history'][] = $history;
    return $all;
}
/**
 * @param bill $bill
 */
function mtn_pre_builder($bill) {
    $all['info'] = $bill->to_array();
    $all['info']['type_html'] = 'جملة MTN مسبق الدفع';
    $all['info']['company'] = 'MTN';
    $all['info']['price'] = $bill->price;
    $all['info']['infos'] = 'الكمية : '.$bill->amount.'<br>الرقم : '.$bill->phone .'<br>الكود : '.data::$get->code;
    $field['name'] = 'الشركة';
    $field['value'] = 'MTN';
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'كمية التعبئة';
    $field['value'] = $bill->amount;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'رقم الهاتف';
    $field['value'] = $bill->phone;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الكود';
    $field['value'] = data::$get->code;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المدينة';
    $field['value'] = data::$get->city;
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الاسم';
    $field['value'] =data::$get->name;
    $field['copy_btn'] = false;
    $field['hide_from_staff'] = true;
    $all['info']['fields'][] = $field;
    $history['type'] = 'new';
    $history['date'] = date(config::$get->storedatetime);
    $all['history'][] = $history;
    return $all;
}
/**
 * @param bill $bill
 */
function syt_pre_builder($bill) {
    $all['info'] = $bill->to_array();
    $all['info']['type_html'] = 'جملة SYRIATEL مسبق الدفع';
    $all['info']['company'] = 'SYRIATEL';
    $all['info']['price'] = $bill->price;
    $all['info']['infos'] = 'المبلغ : '.$bill->amount.'<br>الكود : '.$bill->phone;
    $field['name'] = 'الشركة';
    $field['value'] = 'SYRIATEL';
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المبلغ';
    $field['value'] = $bill->amount;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الكود';
    $field['value'] = $bill->phone;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المدينة';
    $field['value'] = data::$get->city;
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الاسم';
    $field['value'] =data::$get->name;
    $field['copy_btn'] = false;
    $field['hide_from_staff'] = true;
    $all['info']['fields'][] = $field;
    $history['type'] = 'new';
    $history['date'] = date(config::$get->storedatetime);
    $all['history'][] = $history;
    return $all;
}
/**
 * @param bill $bill
 */
function mtn_cash_builder($bill) {
    $all['info'] = $bill->to_array();
    $all['info']['type_html'] = 'جملة MTN كاش';
    $all['info']['company'] = 'MTN';
    $all['info']['price'] = $bill->price;
    $all['info']['infos'] = 'المبلغ : '.$bill->amount.'<br>الرقم : '.$bill->phone.'<br>الكود : '.data::$get->code;
    $field['name'] = 'الشركة';
    $field['value'] = 'MTN';
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المبلغ';
    $field['value'] = $bill->amount;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'رقم الهاتف';
    $field['value'] = $bill->phone;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الكود';
    $field['value'] = data::$get->code;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المدينة';
    $field['value'] = data::$get->city;
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الاسم';
    $field['value'] =data::$get->name;
    $field['copy_btn'] = false;
    $field['hide_from_staff'] = true;
    $all['info']['fields'][] = $field;
    $history['type'] = 'new';
    $history['date'] = date(config::$get->storedatetime);
    $all['history'][] = $history;
    return $all;
}
/**
 * @param bill $bill
 */
function syt_cash_builder($bill) {
    $all['info'] = $bill->to_array();
    $all['info']['type_html'] = 'جملة SYRIATEL كاش';
    $all['info']['company'] = 'SYRIATEL';
    $all['info']['price'] = $bill->price;
    $all['info']['infos'] = 'المبلغ : '.$bill->amount.'<br>الكود : '.$bill->phone;
    $field['name'] = 'الشركة';
    $field['value'] = 'SYRIATEL';
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المبلغ';
    $field['value'] = $bill->amount;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الكود';
    $field['value'] = $bill->phone;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المدينة';
    $field['value'] = data::$get->city;
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الاسم';
    $field['value'] =data::$get->name;
    $field['copy_btn'] = false;
    $field['hide_from_staff'] = true;
    $all['info']['fields'][] = $field;
    $history['type'] = 'new';
    $history['date'] = date(config::$get->storedatetime);
    $all['history'][] = $history;
    return $all;
}