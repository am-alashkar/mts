<?php

/**
 * Info received will overwrite all previous data
 * IF and ONLY IF info is correct .
 */

unset($final_array);
unset($found_info);
unset($e);
foreach ($info[0] as $k => $v) {
    $hdr[$v] = $k;
}
unset($info[0]);
if (!isset($hdr['الاسم'])|| !isset($hdr['السعر']) || !isset($hdr['المبلغ']) || !isset($hdr['النوع']) || !isset($hdr['ملاحظات'])  ) {
    unset($info);
}
//dumb($info);
/** @var array $found_info */
if ($info) {
    foreach ($info as $item) {
        $e['name'] =  $item[$hdr['الاسم']];
        $e['price'] = $item[$hdr['السعر']];
        $e['adder'] = $item[$hdr['المبلغ']];
        $e['note'] = $item[$hdr['ملاحظات']];
        $e['type'] = $item[$hdr['النوع']];
        $found_info[$e['name']][] = $e;
    }
    $fk = 'MTN كاش';
    if ($found_info[$fk][0]) {
        $r = $found_info[$fk][0];
        if ($r['note'] == 'مبلغ ثابت') {
            $final_array['mtn_cash_adder'] = $r['adder'];
        }
        $final_array['mtn_cash_price'] = $r['price'];
    }
    $fk = 'Syriatel كاش';
    if ($found_info[$fk][0]) {
        $r = $found_info[$fk][0];
        if ($r['note'] == 'مبلغ ثابت') {
            $final_array['syt_cash_adder'] = $r['adder'];
        }
        $final_array['syt_cash_price'] = $r['price'];
    }
    $fk = 'Wafatel كاش';
    if ($found_info[$fk][0]) {
        $r = $found_info[$fk][0];
        if ($r['note'] == 'مبلغ ثابت') {
            $final_array['wfa_cash_adder'] = $r['adder'];
        }
        $final_array['wfa_cash_price'] = $r['price'];
    }
    $fk = 'MTN لاحق الدفع';
    if ($found_info[$fk][0]) {
        $r = $found_info[$fk][0];
        if ($r['note'] == 'مبلغ ثابت') {
            $final_array['mtn_pos_adder'] = $r['adder'];
        }
        $final_array['mtn_pos_price'] = $r['price'];
    }
    $fk = 'Syriatel لاحق الدفع';
    if ($found_info[$fk][0]) {
        $r = $found_info[$fk][0];
        if ($r['note'] == 'مبلغ ثابت') {
            $final_array['syt_pos_adder'] = $r['adder'];
        }
        $final_array['syt_pos_price'] = $r['price'];
    }
    $fk = 'Wafatel لاحق الدفع';
    if ($found_info[$fk][0]) {
        $r = $found_info[$fk][0];
        if ($r['note'] == 'مبلغ ثابت') {
            $final_array['wfa_pos_adder'] = $r['adder'];
        }
        $final_array['wfa_pos_price'] = $r['price'];
    }
    $fk = 'mtn مسبق الدفع';
    if ($found_info[$fk][0]) {
        $r = $found_info[$fk];
        foreach ($r as $rr) {
            $final_array['mtn_prepaid'][$rr['type']] = $rr['price'];
        }
    }
    $fk = 'Syriatel مسبق الدفع';
    if ($found_info[$fk][0]) {
        $r = $found_info[$fk];
        foreach ($r as $rr) {
            $final_array['syt_prepaid'][$rr['type']] = $rr['price'];
        }
    }
    $fk = 'Wafatel مسبق الدفع';
    if ($found_info[$fk][0]) {
        $r = $found_info[$fk];
        foreach ($r as $rr) {
            $final_array['wfa_prepaid'][$rr['type']] = $rr['price'];
        }
    }
}
$sql = "WHERE user_id = '".$user_id."' AND script = 'mobile_retail' ";
db::$db->delete('custom_prices',null,null,null,$sql);
//$c_s = db::$db->adv_select('*','custom_prices',$sql);
if (is_array($final_array)) {
    $in['info'] = data::encode($final_array);
    $in['script'] = 'mobile_retail';
    $in['user_id'] = $user_id;
    db::$db->insert('custom_prices',$in);
    $final_ans .= 'رصيد جوال مفرق<br>';
}
