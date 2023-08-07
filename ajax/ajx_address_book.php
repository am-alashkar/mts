<?php
if (!member::$current->id) die();

if (data::$get->type)
{
    switch (data::$get->btn)
    {
        case 'add':
            add_ab();
            die();
        case 'delete':
        case 'edit':
            delete_ab();
            die();
        default:
            break;
    }
    $sql = "WHERE user_id = '".member::$current->id."' AND type = '".data::$get->type."' ";
    $list = db::$db->adv_select('*','address_book',$sql);
    $html = new style('address_book');
    $html->fill_table('address',$list,true)->create_modal('دفتر العناوين');
    print $html;
    die();
}
function add_ab() {
    if (!data::$get->id) die();
    $bill = new bill(data::$get->id);
    if (!$bill->can_view()) {
        create_error('الطلب غير موجود أو لا يمكنك مشاهدته');
        die();
    }
    $b = $bill->html_vars();
//dd($bill);\
    if ($bill->script == 'mobile_kazia')
    {
        $fields = $b['fields'];
        $name = $city = $company = $code = $phone = '';
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
                case 'الاسم':
                    $name = $field['value'];
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
                $in['type'] = 'mobile_kazia';
                $in['user_id'] = member::get_id();
                $in['code'] = $code;
                $in['phone'] = $phone ?? '';
                $in['city'] = $city;
                $in['company'] = $company;
                $in['name'] = $name;
                db::$db->insert('address_book',$in);
                print '<span style="color: darkgreen"><i class="fa fa-check"></i> </span>';
            } else {
                create_error('البيانات موجودة مسبقاً');
            }
        }

    } else {
        create_error('الطلب ليس جملة كازيات');
    }
}
function delete_ab(){
    if (!data::$get->del_id) die('no id');
    $add = db::$db->select('*','address_book','id','=',data::$get->del_id)->first();
    if ($add && $add['user_id'] == member::$current->id)
    {
        db::$db->delete('address_book','id','=',data::$get->del_id);
        print style::success_gritter('تم الحذف',false);
        print '<script>
$("#name_'.data::$get->del_id.'").parent().remove();
</script>';
        die();
    }
}