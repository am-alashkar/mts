<?php
/**
 * Client ONLY KAZIA
 *  Generate items and prices
 *  Each item with base price
 */

if (!$user_id) create_error('Too few arguments received in price function.');
$mem = member::load_member($user_id);
if ($mem->deleted || $mem->disabled)
{
    $prs = null;
} else {

    $info = file_get_contents('./data/mobile_kazia.prs');
    $prs = data::decode($info);
    $tmp = explode("\n",$prs['syt_cities_in']);
    $prices['syt_cities'] = '';
    $prices['mtn_cities'] = '';
    foreach ($tmp as $item) {
        $prices['syt_cities'] .= '<option value="'.$item.'">'.$item.'</option>';
        $prices['syt_cits'][] = $item;
    }
    $tmp = explode("\n",$prs['mtn_cities_in']);
    foreach ($tmp as $item) {
        $prices['mtn_cities'] .= '<option value="'.$item.'">'.$item.'</option>';
        $prices['mtn_cits'][] = $item;
    }
    $sql = "WHERE user_id = '".$user_id."' AND script = 'mobile_kazia' ";
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
    //if (!$prices['mtn_cash_price']) $prices['mtn_cash_price'] = 0;
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
    if (+$prs['mtn_pre_price_0'] >0 && +$prs['mtn_pre_price_'.$mem->price_group] >0) {
        $prices['mtn_pre_type'] = $prs['mtn_pre_type'];
        if ($prices['mtn_pre_type'] == 'true') {
            $prices['mtn_pre_price'] =  +$prs['mtn_pre_price_0'];
            $prices['mtn_pre_adder'] = +$prs['mtn_pre_price_' . $mem->price_group];
            if (isset($custom_prices['mtn_pre_adder'])) {
                if ($custom_prices['mtn_pre_price']<1) {
                    unset($prices['mtn_pre_price']);
                    unset($prices['mtn_pre_adder']);
                    // unset($prices['mtn_pre_type']);
                } else {
                    $prices['mtn_pre_adder'] = +$custom_prices['mtn_pre_adder'];
                    $prices['mtn_pre_price'] = +$custom_prices['mtn_pre_price'];

                }
            }
        } else
        {
            $prices['mtn_pre_price'] = max(+$prs['mtn_pre_price_' . $mem->price_group], +$prs['mtn_pre_price_0']);
            $prices['mtn_pre_adder'] = 0;
            if (isset($custom_prices['mtn_pre_price']) ) {
                if ($custom_prices['mtn_pre_price']<1) {
                    unset($prices['mtn_pre_price']);
                    unset($prices['mtn_pre_adder']);
                    //unset($prices['mtn_pre_type']);
                } else {
                    $prices['mtn_pre_price'] = max(+$custom_prices['mtn_pre_price'], +$prs['mtn_pre_price_0']);
                }
            }
        }
    }
    //if (!$prices['mtn_pos_price']) $prices['mtn_pos_price'] = 0;
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
    if (+$prs['syt_pre_price_0'] >0 && +$prs['syt_pre_price_'.$mem->price_group] >0) {
        $prices['syt_pre_type'] = $prs['syt_pre_type'];
        if ($prices['syt_pre_type'] == 'true') {
            $prices['syt_pre_price'] =  +$prs['syt_pre_price_0'];
            $prices['syt_pre_adder'] = +$prs['syt_pre_price_' . $mem->price_group];
            if (isset($custom_prices['syt_pre_adder'])) {
                if ($custom_prices['syt_pre_price']<1) {
                    unset($prices['syt_pre_price']);
                    unset($prices['syt_pre_adder']);
                    //unset($prices['syt_cash_type']);
                } else {
                    $prices['syt_pre_adder'] = +$custom_prices['syt_pre_adder'];
                    $prices['syt_pre_price'] = +$custom_prices['syt_pre_price'];

                }
            }
        } else
        {
            $prices['syt_pre_price'] = max(+$prs['syt_pre_price_' . $mem->price_group], +$prs['syt_pre_price_0']);
            $prices['syt_pre_adder'] = 0;
            if (isset($custom_prices['syt_pre_price']) ) {
                if ($custom_prices['syt_pre_price']<1) {
                    unset($prices['syt_pre_price']);
                    unset($prices['syt_pre_adder']);
                    //unset($prices['mtn_pre_type']);
                } else {
                    $prices['syt_pre_price'] = max(+$custom_prices['syt_pre_price'], +$prs['syt_pre_price_0']);
                }
            }
        }
    }
}
