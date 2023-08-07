<?php
$bill = new bill(data::$get->id);
$html = new style('view_bill');
if (!$bill->can_view()) {
    print style::big_error_gritter('لا يمكنك عرض هذا الطلب ، أو أن الطلب غير موجود');
    die();
}
$fun_name = data::$get->btn.'_bill';
if (function_exists($fun_name)) $ans = $fun_name($bill);
else create_error('لم يتم العثور على الأمر المطلوب' .' ' . data::$get->btn);
$html = $bill->btns(true)->str_replace('<!-- RID -->',data::$get->rid);
print  $html;
if (!$html) {
   print '<script>

</script>';
}
print '<script>
$("#bill_stat_'.data::$get->rid.'").html(`'.bill::stat_ht($bill).'`);
$("#table_bill_stat_'.$bill->id.'").html(`'.bill::stat_ht($bill).'`);
</script>';

/**
 * @param bill $bill
 */
function take_bill($bill) {
    if ($bill->done) {
        print style::big_error_gritter('تم انهاء الطلب مسبقاً','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    if ($bill->admin_id) {
        print style::big_error_gritter('تم حجز الطلب مسبقاً','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    if (allowed('can_do_bills') && allowed($bill->script.'_staff')) {
        $bill->set_stat(4,data::$get->admin_note);
        $bill->done = null;
        $bill->update();
        return true;
    }
    return false;
}

/**
 * @param bill $bill
 * @return bool
 */
function reject_bill($bill) {
    if ($bill->done) {
        print style::big_error_gritter('تم انهاء الطلب مسبقاً','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    if ($bill->admin_id != member::$current->id) {
        print style::big_error_gritter('تم حجز الطلب من قبل موظف آخر','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    if (allowed('can_do_bills') && allowed($bill->script.'_staff')) {
        if (!data::$get->admin_note) {
            print style::error_gritter('يجب كتابة سبب الالغاء',false);
            print style::set_input('admin_note_'.data::$get->rid,'is-invalid');
            return false;
        }
        $bill->set_stat(2,data::$get->admin_note);
        $bill->done = 1;
        $bill->ba = null;
        $bill->update();
        return true;
    }
    return false;
}
/**
 * @param bill $bill
 * @return bool
 */
function cancel_bill($bill) {
    if ($bill->done) {
        print style::big_error_gritter('تم انهاء الطلب مسبقاً','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    // only same user or sender ..
    if ($bill->user_id != member::$current->id && $bill->sender_id != member::$current->id) {
        print style::big_error_gritter('ليس لديك الصلاحيات المناسبة لالغاء هذا الطلب','لا يمكن اتمام العملية');
        refresh(true,1000);
        die();
    }
    if (!data::$get->admin_note) {
        print style::error_gritter('يجب كتابة سبب الالغاء',false);
        print style::set_input('admin_note_'.data::$get->rid,'is-invalid');
        return false;
    }
    if (!$bill->stat) {
        // ok
        $bill->set_stat(2,data::$get->admin_note);
        $bill->done = 1;
        $bill->ba = null;
        $bill->update();
        return true;
    } else
    if ($bill->stat == '4') {
        // send a cancel request
        $bill->cr = data::encode(['date'=>date(config::$get->storedatetime),'note'=>data::$get->admin_note]);
        $bill->update();
        print style::big_success_gritter('ولكن الطلب قيد التنفيذ من قبل أحد الموظفين ، قد لا يتمكن الموظف من الغاء الطلب في حال بدأ في تنفيذه','تم ارسال طلب الالغاء');
        return true;
    }
    else {
        print style::big_error_gritter('لم يعد بالامكان الغاء الطلب','لا يمكن اتمام العملية');
        refresh(true,1000);
        die();
    }

    return false;
}
/**
 * @param bill $bill
 * @return bool
 */
function hold_bill($bill) {
    if ($bill->done) {
        print style::big_error_gritter('تم انهاء الطلب مسبقاً','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    if ($bill->admin_id != member::$current->id) {
        print style::big_error_gritter('تم حجز الطلب من قبل موظف آخر','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    if (allowed('can_do_bills') && allowed($bill->script.'_staff')) {
        $bill->set_stat(3,data::$get->admin_note);
        $bill->done = null;
        //$bill->ba = 1;
        $bill->update();
        return true;
    }
    return false;
}
/**
 * @param bill $bill
 * @return bool
 */
function un_hold_bill($bill) {
    if ($bill->done) {
        print style::big_error_gritter('تم انهاء الطلب مسبقاً','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    if ($bill->admin_id != member::$current->id) {
        print style::big_error_gritter('تم حجز الطلب من قبل موظف آخر','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    if (allowed('can_do_bills') && allowed($bill->script.'_staff')) {
        $bill->set_stat(4,data::$get->admin_note);
        $bill->done = null;
        //$bill->ba = 1;
        $bill->update();
        return true;
    }
    return false;
}
/**
 * @param bill $bill
 * @return bool
 */
function accept_bill($bill) {
    if ($bill->done) {
        print style::big_error_gritter('تم انهاء الطلب مسبقاً','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    if ($bill->admin_id != member::$current->id) {
        print style::big_error_gritter('تم حجز الطلب من قبل موظف آخر','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
    if (allowed('can_do_bills') && allowed($bill->script.'_staff')) {
        $bill->set_stat(1,data::$get->admin_note);
        $bill->done = 1;
        $bill->ba = 1;
        $r = $bill->update();
        if (!$r) {
            print  style::big_error_gritter('حدث خطأ ولا يمكن المتابعة');
            return false;
        }
        if ($r < 0) {
            print style::big_error_gritter('رصيد الزبون غير كافي لتنفيذ الطلب ، يرجى الغاء الطلب أو زيادة الرصيد الائتماني للزبون','لا يمكن المتابعة');
            $bill->reload();
            return false;
        }
        return true;
    }
    //print 'Err';
    return false;
}
/**
 * @param bill $bill
 * @return bool
 */
function overtake_bill($bill) {
    if ($bill->done) {
        print style::big_error_gritter('تم انهاء الطلب مسبقاً','لا يمكن اتمام العملية');
        refresh(true,3000);
        die();
    }
//    if ($bill->admin_id != member::$current->id) {
//        print style::big_error_gritter('تم حجز الطلب من قبل موظف آخر','لا يمكن اتمام العملية');
//        refresh(true,3000);
//        die();
//    }
    if (allowed('can_do_bills') && allowed($bill->script.'_staff')) {
        $bill->set_stat($bill->stat,data::$get->admin_note);

        $r = $bill->update();
        if (!$r) {
            print  style::big_error_gritter('حدث خطأ ولا يمكن المتابعة');
            return false;
        }
        if ($r < 0) {
            print style::big_error_gritter('رصيد الزبون غير كافي لتنفيذ الطلب ، يرجى الغاء الطلب أو زيادة الرصيد الائتماني للزبون','لا يمكن المتابعة');
            $bill->reload();
            return false;
        }
        return true;
    }
    //print 'Err';
    return false;
}