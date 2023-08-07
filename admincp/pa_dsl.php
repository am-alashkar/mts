<?php
data::$get->page_title = 'إدارة خدمة DSL';
if (!allowed('pers_dsl_manage')) {
    print 'Not Allowed';
    redirect_to_index();
}
$info = data::decode(file_get_contents('./data/dsl.prs'));
$html = new style('admin_price_dsl');
$companies = $info['company_list'];
if (data::$get->all[2]) {
    // == company -> company edit page by all[3] value // edit name , services , also all is javascript and save btn for all .. import from xls ?(
    // == new -> new company page -> all javascript ? import from another company ? etc
    // == names -> ?? address book names but this must be in an other page.
    // delete is ajax , disable is ajax ,
    data::$get->all[2] = urldecode(data::$get->all[2]);
    $comName = data::$get->all[2];
    if (!$info['services'][$comName]) history_back();
    $d['name'] = $comName;
    $d['static_ip'] = $info['services'][$comName]['static_ip']['type'];
    $d['static_ip_price_0'] =$info['services'][$comName]['static_ip']['price_0'];
    $d['static_ip_price_1'] =$info['services'][$comName]['static_ip']['price_1'];
    $d['static_ip_price_2'] =$info['services'][$comName]['static_ip']['price_2'];
    $d['static_ip_price_3'] =$info['services'][$comName]['static_ip']['price_3'];
    $d['is_enabled'] = style::check_box('is_enabled','تفعيل الشركة','general',$info['com_option'][$comName]['enabled']);
    $d['special_amount'] = style::check_box('special_amount','تمكين تسديد مبالغ مخصصة','general',$info['com_option'][$comName]['special_amount']['enabled']);
    $d['special_amount_price_0'] =$info['services'][$comName]['special_amount']['price_0'];
    $d['special_amount_price_1'] =$info['services'][$comName]['special_amount']['price_1'];
    $d['special_amount_price_2'] =$info['services'][$comName]['special_amount']['price_2'];
    $d['special_amount_price_3'] =$info['services'][$comName]['special_amount']['price_3'];
    //dd($d);
    $html->part(3)->fill($d);

} else
{
    // main page
    // find companies from info
    $companies = $info['company_list'];
    foreach ($companies as $company) {
        $cominfo['name'] = $company;
        if (!$info['com_option'][$company]['enabled'])
        {
            $btn = '<i class="fa fa-exclamation-triangle text-warning" ></i>';
        } else {
            $btn = '';
        }
        $btn .= '<a href="'.config::$get->home_link.'prices/dsl/'.$company.'/" class="btn btn-success btn-sm"><i class="fa fa-arrow-alt-circle-left"></i></a>';
        $cominfo['btns'] = $btn;
        $com_list[] = $cominfo;
    }
    $html->part(1)->fill_table('com_list',$com_list,true);
}
job::$body = $html;