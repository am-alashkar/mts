<?php
if (!member::check()) redirect_to_index();
switch (data::$get->btn)
{
    case 'new':
        new_card();
        break;
    case 'confirm_new':
        confirm_new_card();
        break;
    default:
        create_error('Unknown Error');
}
//dd();
function new_card()
{
    /** Show modal of new card  */
    if (!allowed('is_agent')) create_error('Not Allowed');
    if (allowed('is_accountant')) data::$get->csrf = member::get_id();
    else data::$get->csrf = gen_uuid();
    $html = new style('new_payment_card');
    $html->create_modal('بطاقة تعبئة رصيد جديدة');
    print $html;
    die();
}
function confirm_new_card() {
    /**
     * create card
     * add to bills
     * update card with bill id
     * open bill that contain the card .
     * update the table
     *
     */
    $num = member::get_id().time();
    $code = rand(100,900)%9;
    $code++;
    for ($i =0 ;$i<9;$i++) $code.=rand(100,900)%9;
    if (!is_numeric(data::$get->amount) || data::$get->amount < 500)
    {
        die('<span style="color: red">المبلغ غير صحيح ، الحد الأدنى 500 ليرة</span>');
    }
    $in = new result();
    $in->amount = +data::$get->amount;
    $in->num = $num;
    $in->code = $code;
    $in->maker = member::get_id();
    $in->abill_id = 0;
    $in->notes = data::$get->notes;
    $in->make_date = date(config::$get->storedatetime);
    $id = db::$db->insert('unused_cards',$in);
    if (!$id) create_error('حدث خطأ داخلي'.db::$db->get_last_error());
    $bill = new bill();
    $bill->script = 'payment_card';
    $bill->sub_script = 'create';
    $bill->user_id = member::get_family_id();
    $bill->price = $in->amount;
    $bill->sender_id = member::get_id();
    $bill->amount = $in->amount;
    $bill->balance();
    if (!$bill->is_ok())
    {
        print style::error_gritter('رصيدك غير كافي لاتمام العملية');
        db::$db->delete('unused_cards','id','=',$id);
        die();
    }
    $all['info'] = $bill->to_array();
    $all['info']['type_html'] = 'انشاء كود تعبئة رصيد';
    $all['info']['price'] = $bill->price;
    $all['info']['infos'] = 'المبلغ : '.$bill->amount;//.'<br>الرقم : '.$bill->phone;
    $field['name'] = 'الرقم';
    $field['value'] = $num;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'الكود';
    $field['value'] = $code;
    $field['copy_btn'] = true;
    $all['info']['fields'][] = $field;
    $field['name'] = 'المبلغ';
    $field['value'] = $in->amount;
    $field['copy_btn'] = false;
    $all['info']['fields'][] = $field;
    $history['type'] = 'new';
    $history['date'] = date(config::$get->storedatetime);
    $all['history'][] = $history;
    $bill->info = $all['info'];
    $bill->history = $all['history'];
    $bill->ba = !allowed('is_accountant');
    $bill->set_stat(1);
    $bid = $bill->insert();
    $up['abill_id'] = $bid;
    if (!$bid)
    {
        print db::$db->get_last_error();
        create_error('خطأ داخلي');
    }
    db::$db->update('unused_cards',$up,'id','=',$id);
    data::$get->id = $bid;
    print style::big_success_gritter('رقم الطلب '.$bid,'تم تنفيذ العملية بنجاح');
    refresh(true,2000);
    //require 'ajx_view_bill.php';
}