<?php
/** Price admin
 * Mobile retail prices / Services
 *
 */
data::$get->page_title = '{{pers_mobile_retail_price_admin}}';
if (!allowed('pers_mobile_retail_price')) {
    print 'Not Allowed';
    redirect_to_index();
}
$html = new style('admin_price_mobile_kazia');
$info = data::decode(file_get_contents('./data/mobile_kazia.prs'));
$check_box = style::check_box('mtn_cash_type','','mtn_general',$info['mtn_cash_type'] == 'true');
$html->str_replace('<!-- MTN_CASH_IS_NOT_PERCENT -->',$check_box['checkbox']);
$check_box = style::check_box('mtn_pos_type','','mtn_general',$info['mtn_pos_type'] == 'true');
$html->str_replace('<!-- MTN_POS_IS_NOT_PERCENT -->',$check_box['checkbox']);
$check_box = style::check_box('mtn_pre_type','','mtn_general',$info['mtn_pre_type'] == 'true');
$html->str_replace('<!-- MTN_PRE_IS_NOT_PERCENT -->',$check_box['checkbox']);
$check_box = style::check_box('syt_cash_type','','syt_general',$info['syt_cash_type'] == 'true');
$html->str_replace('<!-- SYT_CASH_IS_NOT_PERCENT -->',$check_box['checkbox']);
$check_box = style::check_box('syt_pos_type','','syt_general',$info['syt_pos_type'] == 'true');
$html->str_replace('<!-- SYT_POS_IS_NOT_PERCENT -->',$check_box['checkbox']);
$check_box = style::check_box('syt_pre_type','','syt_general',$info['syt_pre_type'] == 'true');
$html->str_replace('<!-- SYT_PRE_IS_NOT_PERCENT -->',$check_box['checkbox']);
$check_box = style::check_box('wfa_cash_type','','wfa_general',$info['wfa_cash_type'] == 'true');
$html->str_replace('<!-- WFA_CASH_IS_NOT_PERCENT -->',$check_box['checkbox']);
$check_box = style::check_box('wfa_pos_type','','wfa_general',$info['wfa_pos_type'] == 'true');
$html->str_replace('<!-- WFA_POS_IS_NOT_PERCENT -->',$check_box['checkbox']);
$check_box = style::check_box('wfa_pre_type','','wfa_general',$info['wfa_pre_type'] == 'true');
$html->str_replace('<!-- WFA_PRE_IS_NOT_PERCENT -->',$check_box['checkbox']);
//var_dump($info);
//die();
$html->fill($info);

job::$body =  $html;
