<?php
print '-'; // important to print -
$bill = new bill(data::$get->id);
$html = new style('view_bill');
if (!$bill->can_view()) {
    print style::big_error_gritter('لا يمكنك عرض هذا الطلب ، أو أن الطلب غير موجود');
    die();
}
$b = $bill->html_vars();
//dd($bill);\
if ($bill->script == 'mobile_kazia' && $bill->user_id == member::get_id())
{
   $fields = $b['fields'];
   $company = $code = $phone = '';
    foreach ($fields as $field) {
        switch (trim((string)$field['name'])) {
            case 'الشركة':
                $company = $field['value'];
                break;
            case 'الكود':
                $code = $field['value'];
                break;
            case 'رقم الهاتف':
            case 'الرقم':
                $phone = $field['value'];
                break;
            case 'المدينة':
            case 'المحافظة':
                $city = $field['value'];
                break;
            default:
                break;
        }
    }
    if ($company && ( $phone || $company == 'SYRIATEL') && $code)
    {
        $sql = "WHERE type='mobile_kazia' AND user_id = '".member::get_id()."' AND code='".$code."' AND phone='".$phone."' AND company = '".$company."';";
        $ans = db::$db->adv_select('*','address_book',$sql)->first();
        if (!$ans){
            $f['name']='إضافة إلى دليل العناوين';
            $f['value'] = '<span id="add_address_<!-- RID -->"><button class="btn btn-warning" 
onclick="senddata(\'add_address_<!-- RID -->\',\'todo=address_book&type=mobile_kazia&btn=add&id='.data::$get->id.'\')"
><i class="fa fa-plus"></i> انقر هنا للاضافة إلى دفتر العناوين</button></span>';
            $b['info']['fields'][] = $f;
        } else {
            //dd($ans);
        }
    }

}
$html->fill_table('bill_table',$b['info']['fields'],true)->fill($b)
    ->create_modal('الطلب رقم '.$bill->id );

print $html;