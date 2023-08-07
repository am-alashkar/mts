<?php
/**
 * Client ONLY
 *  Generate items and prices
 *  Each item with base price
 */

if (!$user_id) create_error('Too few arguments received in price function.');
$mem = member::load_member($user_id);
if ($mem->deleted || $mem->disabled)
{
    $prs = null;
} else {

    $info = file_get_contents('./data/mobile_retail.prs');
    $prs = data::decode($info);

    $sql = "WHERE user_id = '".$user_id."' AND script = 'mobile_retail' ";
    $custom_prices = db::$db->adv_select('*','custom_prices',$sql);
    $custom_prices = data::decode($custom_prices->first()['info']);
    if (+$prs['mtn_cash_price_0'] >0 && +$prs['mtn_cash_price_'.$mem->price_group] >0 ) {
        $prices['mtn_cash_type'] = $prs['mtn_cash_type'];
        if ($prices['mtn_cash_type'] == 'true') {
            $prices['mtn_cash_price'] =  +$prs['mtn_cash_price_0'];
            $prices['mtn_cash_adder'] = +$prs['mtn_cash_price_' . $mem->price_group];
            if (isset($custom_prices['mtn_cash_adder'])) {
                if ($custom_prices['mtn_cash_price']<1) {
                    unset($prices['mtn_cash_price']);
                    unset($prices['mtn_cash_adder']);
                    //unset($prices['mtn_cash_type']);
                } else {
                    $prices['mtn_cash_adder'] = +$custom_prices['mtn_cash_adder'];
                    $prices['mtn_cash_price'] = +$custom_prices['mtn_cash_price'];
                }
            }
            // if not set it means the custom price was added when the price was not an added amount so it must be ignored
        } else
        {
            $prices['mtn_cash_price'] = max(+$prs['mtn_cash_price_' . $mem->price_group], +$prs['mtn_cash_price_0']);
            $prices['mtn_cash_adder'] = 0;
            if (isset($custom_prices['mtn_cash_price']) ) {
                if ($custom_prices['mtn_cash_price']<1) {
                    unset($prices['mtn_cash_price']);
                    unset($prices['mtn_cash_adder']);
                    //unset($prices['mtn_cash_type']);
                } else {
                    $prices['mtn_cash_price'] = max(+$custom_prices['mtn_cash_price'], +$prs['mtn_cash_price_0']);
                }
            }

        }
    }
    if (+$prs['mtn_pos_price_0'] >0 && +$prs['mtn_pos_price_'.$mem->price_group] >0) {
        $prices['mtn_pos_type'] = $prs['mtn_pos_type'];
        if ($prices['mtn_pos_type'] == 'true') {
            $prices['mtn_pos_price'] =  +$prs['mtn_pos_price_0'];
            $prices['mtn_pos_adder'] = +$prs['mtn_pos_price_' . $mem->price_group];
            if (isset($custom_prices['mtn_pos_adder'])) {
                if ($custom_prices['mtn_pos_price']<1) {
                    unset($prices['mtn_pos_price']);
                    unset($prices['mtn_pos_adder']);
                   // unset($prices['mtn_pos_type']);
                } else {
                    $prices['mtn_pos_adder'] = +$custom_prices['mtn_pos_adder'];
                    $prices['mtn_pos_price'] = +$custom_prices['mtn_pos_price'];

                }
            }
        } else
        {
            $prices['mtn_pos_price'] = max(+$prs['mtn_pos_price_' . $mem->price_group], +$prs['mtn_pos_price_0']);
            $prices['mtn_pos_adder'] = 0;
            if (isset($custom_prices['mtn_pos_price']) ) {
                if ($custom_prices['mtn_pos_price']<1) {
                    unset($prices['mtn_pos_price']);
                    unset($prices['mtn_pos_adder']);
                    //unset($prices['mtn_pos_type']);
                } else {
                    $prices['mtn_pos_price'] = max(+$custom_prices['mtn_pos_price'], +$prs['mtn_pos_price_0']);
                }
            }
        }
    }
    if (+$prs['syt_cash_price_0'] >0 && +$prs['syt_cash_price_'.$mem->price_group] >0) {
        $prices['syt_cash_type'] = $prs['syt_cash_type'];
        if ($prices['syt_cash_type'] == 'true') {
            $prices['syt_cash_price'] =  +$prs['syt_cash_price_0'];
            $prices['syt_cash_adder'] = +$prs['syt_cash_price_' . $mem->price_group];
            if (isset($custom_prices['syt_cash_adder'])) {
                if ($custom_prices['syt_cash_price']<1) {
                    unset($prices['syt_cash_price']);
                    unset($prices['syt_cash_adder']);
                    //unset($prices['syt_cash_type']);
                } else {
                    $prices['syt_cash_adder'] = +$custom_prices['syt_cash_adder'];
                    $prices['syt_cash_price'] = +$custom_prices['syt_cash_price'];
                }
            }
        } else
        {
            $prices['syt_cash_price'] = max(+$prs['syt_cash_price_' . $mem->price_group], +$prs['syt_cash_price_0']);
            $prices['syt_cash_adder'] = 0;
            if (isset($custom_prices['syt_cash_price']) ) {
                if ($custom_prices['syt_cash_price']<1) {
                    unset($prices['syt_cash_price']);
                    unset($prices['syt_cash_adder']);
                    //unset($prices['mtn_pos_type']);
                } else {
                    $prices['syt_cash_price'] = max(+$custom_prices['syt_cash_price'], +$prs['syt_cash_price_0']);
                }
            }
        }
    }
    if (+$prs['syt_pos_price_0'] >0 && +$prs['syt_pos_price_'.$mem->price_group] >0) {
        $prices['syt_pos_type'] = $prs['syt_pos_type'];
        if ($prices['syt_pos_type'] == 'true') {
            $prices['syt_pos_price'] =  +$prs['syt_pos_price_0'];
            $prices['syt_pos_adder'] = +$prs['syt_pos_price_' . $mem->price_group];
            if (isset($custom_prices['syt_pos_adder'])) {
                if ($custom_prices['syt_pos_price']<1) {
                    unset($prices['syt_pos_price']);
                    unset($prices['syt_pos_adder']);
                    //unset($prices['syt_cash_type']);
                } else {
                    $prices['syt_pos_adder'] = +$custom_prices['syt_pos_adder'];
                    $prices['syt_pos_price'] = +$custom_prices['syt_pos_price'];

                }
            }
        } else
        {
            $prices['syt_pos_price'] = max(+$prs['syt_pos_price_' . $mem->price_group], +$prs['syt_pos_price_0']);
            $prices['syt_pos_adder'] = 0;
            if (isset($custom_prices['syt_pos_price']) ) {
                if ($custom_prices['syt_pos_price']<1) {
                    unset($prices['syt_pos_price']);
                    unset($prices['syt_pos_adder']);
                    //unset($prices['mtn_pos_type']);
                } else {
                    $prices['syt_pos_price'] = max(+$custom_prices['syt_pos_price'], +$prs['syt_pos_price_0']);
                }
            }
        }
    }
    if (+$prs['wfa_cash_price_0'] >0 && +$prs['wfa_cash_price_'.$mem->price_group] >0) {
        $prices['wfa_cash_type'] = $prs['wfa_cash_type'];
        if ($prices['wfa_cash_type'] == 'true') {
            $prices['wfa_cash_price'] =  +$prs['wfa_cash_price_0'];
            $prices['wfa_cash_adder'] = +$prs['wfa_cash_price_' . $mem->price_group];
            if (isset($custom_prices['wfa_cash_adder'])) {
                if ($custom_prices['wfa_cash_price']<1) {
                    unset($prices['wfa_cash_price']);
                    unset($prices['wfa_cash_adder']);
                } else {
                    $prices['wfa_cash_adder'] = +$custom_prices['wfa_cash_adder'];
                    $prices['wfa_cash_price'] = +$custom_prices['wfa_cash_price'];
                }
            }
        } else
        {
            $prices['wfa_cash_price'] = max(+$prs['wfa_cash_price_' . $mem->price_group], +$prs['wfa_cash_price_0']);
            $prices['wfa_cash_adder'] = 0;
            if (isset($custom_prices['wfa_cash_price']) ) {
                if ($custom_prices['wfa_cash_price']<1) {
                    unset($prices['wfa_cash_price']);
                    unset($prices['wfa_cash_adder']);
                    //unset($prices['wfa_pos_type']);
                } else {
                    $prices['wfa_cash_price'] = max(+$custom_prices['wfa_cash_price'], +$prs['wfa_cash_price_0']);
                }
            }
        }
    }
    if (+$prs['wfa_pos_price_0'] >0 && +$prs['wfa_pos_price_'.$mem->price_group] >0) {
        $prices['wfa_pos_type'] = $prs['wfa_pos_type'];
        if ($prices['wfa_pos_type'] == 'true') {
            $prices['wfa_pos_price'] =  +$prs['wfa_pos_price_0'];
            $prices['wfa_pos_adder'] = +$prs['wfa_pos_price_' . $mem->price_group];
            if (isset($custom_prices['wfa_pos_adder'])) {
                if ($custom_prices['wfa_pos_price']<1) {
                    unset($prices['wfa_pos_price']);
                    unset($prices['wfa_pos_adder']);
                    //unset($prices['syt_cash_type']);
                } else {
                    $prices['wfa_pos_adder'] = +$custom_prices['wfa_pos_adder'];
                    $prices['wfa_pos_price'] = +$custom_prices['wfa_pos_price'];

                }
            }
        } else
        {
            $prices['wfa_pos_price'] = max(+$prs['wfa_pos_price_' . $mem->price_group], +$prs['wfa_pos_price_0']);
            $prices['wfa_pos_adder'] = 0;
            if (isset($custom_prices['wfa_pos_price']) ) {
                if ($custom_prices['wfa_pos_price']<1) {
                    unset($prices['wfa_pos_price']);
                    unset($prices['wfa_pos_adder']);
                    //unset($prices['mtn_pos_type']);
                } else {
                    $prices['wfa_pos_price'] = max(+$custom_prices['wfa_pos_price'], +$prs['wfa_pos_price_0']);
                }
            }
        }
    }
    foreach ($prs['syt_prepaid'] as $price) {
        if (+$price['amount'] > 0 &&
        +$price['price_0'] > 0 &&
        +$price['price_'.$mem->price_group] > 0)
        {

            $item['amount'] = +$price['amount'];
            $item['price'] = max(+$price['price_0'],+$price['price_'.$mem->price_group]);
            if (isset($custom_prices['syt_prepaid'][$item['amount']])) {
                if ($custom_prices['syt_prepaid'][$item['amount']] > 0) {
                    $item['price'] = max(+$price['price_0'],+$custom_prices['syt_prepaid'][$item['amount']]);
                } else {
                    $item['price'] = 0;
                }
            }
            $prices['syt_prepaid'][] = $item;

        }
    }
    foreach ($prs['mtn_prepaid'] as $price) {
        if (+$price['amount'] > 0 &&
            +$price['price_0'] > 0 &&
            +$price['price_'.$mem->price_group] > 0)
        {
            $item['amount'] = +$price['amount'];
            $item['price'] = max(+$price['price_0'],+$price['price_'.$mem->price_group]);
            if (isset($custom_prices['mtn_prepaid'][$item['amount']])) {
                if ($custom_prices['mtn_prepaid'][$item['amount']] > 0) {
                    $item['price'] = max(+$price['price_0'],+$custom_prices['mtn_prepaid'][$item['amount']]);
                    //$prices['mtn_prepaid'][] = $item;
                } else {
                    $item['price'] = 0;
                }
            }
            $prices['mtn_prepaid'][] = $item;
        }
    }
    foreach ($prs['wfa_prepaid'] as $price) {
        if (+$price['amount'] > 0 &&
            +$price['price_0'] > 0 &&
            +$price['price_'.$mem->price_group] > 0)
        {
            $item['amount'] = +$price['amount'];
            $item['price'] = max(+$price['price_0'],+$price['price_'.$mem->price_group]);
            if (isset($custom_prices['wfa_prepaid'][$item['amount']])) {
                if ($custom_prices['wfa_prepaid'][$item['amount']] > 0) {
                    $item['price'] = max(+$price['price_0'],+$custom_prices['wfa_prepaid'][$item['amount']]);
                    //$prices['mtn_prepaid'][] = $item;
                } else {
                    $item['price'] = 0;
                }
            }
            $prices['wfa_prepaid'][] = $item;
        }
    }
}
