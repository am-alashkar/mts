<?php
$html = new style('client_report_mp');
$btn1 = $html->get_btn('del_btn_<!-- INFO_id -->');
$btn2 = $html->get_btn('edit_btn_<!-- INFO_id -->');
$tmp['id'] = data::$get->id;
$btn1->fill($tmp);
$btn2->fill($tmp);

switch (data::$get->btn)
{
    case 'delete':
        delete_c_price();
        break;
        case 'edit':
        edit_c_price();
        break;
    default:
        create_error('أمر خاطئ');
}
print $btn1."\n".$btn2;
function edit_c_price() {
    $user = db::$db->select('*','customers','id','=',data::$get->id)->first();
    if (!$user) create_error('لا يمكن العثور على الحساب المطلوب');
    if (data::$get->confirm == 1)
    {
        $in = new result();
        $in->name = data::$get->name;
        $in->phone = data::$get->phone;
        $in->phone2 = data::$get->phone2;
        $in->phone3 = data::$get->phone3;
        $in->phone4 = data::$get->phone4;
        $in->phone5 = data::$get->phone5;
        $in->phone6 = data::$get->phone6;
        $in->city = data::$get->city;
        $in->email = data::$get->email;
        $in->address = data::$get->address;
        if (!$in->name)
        {
            print style::error_gritter('يجب ادخال اسم',false);
            create_error('');
        }
        db::$db->update('customers',$in,'id','=',data::$get->id);
        $scr = style::big_success_gritter('الرجاء الانتظار ريثما يتم تحديث الصفحة','تمت العملية بنجاح');
        print $scr;
        refresh(true,1000);
        die();
    }
    $html = new style('edit_customer');
    $html->fill($user)->create_modal('تعديل حساب زبون');
    print $html;

}
function delete_c_price() {
    $user = data::$get->id;

    if (!$user || !is_numeric($user)) create_error('البيانات غير صحيحة');
    $tmp = db::$db->select('*','customers','id','=',data::$get->id)->first();
    if (!$tmp)
    {
        create_error('لا يمكن العثور على الحساب المطلوب');
    }
    //$sql = "WHERE user_id = '".$user."' AND script = '".$script."' ;";
    if (data::$get->confirm == 1)
    {
        $newuser = data::$get->newcustomer;
        if (!$newuser || $newuser == 'null')
        {
            print style::error_gritter('يجب تحديد زبون لنقل الصيانات إليه ',false);
            create_error(' ');
        }
        //die($newuser);
        $tmp = db::$db->select('id','customers','id','=',$newuser)->first();
        if (!$tmp)
        {
            print style::big_error_gritter('الرجاء الانتظار ريثما يتم تحديث الصفحة','حصل خطأ ولا يمكن تحديد الحساب المطلوب');
            refresh(true,1000);
            create_error('');
        }
        $up['customer'] = $newuser;
        db::$db->update('maintenance',$up,'customer','=',data::$get->id);
        db::$db->delete('customers','id','=',data::$get->id);
        print style::big_success_gritter('تم الحذف','الرجاء الانتظار');
        refresh(true,1000);
        die();
    }
    $tmp['total_jobs'] = db::$db->select('COUNT(id) as cnt','maintenance','customer','=',data::$get->id)->first()['cnt'];
    $html = new style('delete_customer');
    $html->fill($tmp)->create_modal('حذف حساب زبون');
    print $html;


}