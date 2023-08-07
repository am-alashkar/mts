<?php
if (!allowed('administration') ) {
    redirect_to_index();
}
$funname = 'stmr_'.data::$get->btn;
if (function_exists($funname)) {
    $funname();
} else {
    create_error('Function Error '.data::$get->btn);
}
function stmr_reset() {
    // TODO rework
    $tmp = db::$db->select('*','robots','id','=',data::$get->id)->first();
    if (!$tmp) create_error('Robot Not Found');
    if ($tmp['is_busy'] == -1 ) {
        print style::error_gritter('يجب اعداد الروبوت أولاً');
        //refresh(true,1000);
        die();
    }
    if ($tmp['is_ready'] != 1 ) {
        print style::error_gritter('الروبوت غير جاهز !! لا يمكن المتابعة');
        //refresh(true,1000);
        die();
    }
    db::$db->update('mobile_robot',['is_busy'=>0],'id','=',data::$get->id);
    print style::success_gritter('تم التعديل بنجاح');
    //refresh(true,1000);
    die();
}
function stmr_recheck_balance() {
    // TODO rework
    $tmp = db::$db->select('*','mobile_robot','id','=',data::$get->id)->first();
    if (!$tmp) create_error('Robot Not Found');
    $info = data::decode($tmp['info']);
    foreach ($info['services'] as $sim=>$service) {
        foreach ($service as $name => $array) {
            unset($info['services'][$sim][$name]['balance_ready']);
        }
    }
    db::$db->update('mobile_robot',['info'=>data::encode($info)],'id','=',data::$get->id);
    print style::success_gritter('تم التعديل بنجاح');
    //refresh(true,1000);
    die();
}
function stmr_rdy_stat() {
    // TODO rework
    $tmp = db::$db->select('*','robots','id','=',data::$get->id)->first();
    if (!$tmp) create_error('Robot Not Found');
    db::$db->update('mobile_robot',['is_ready'=>(data::$get->ready_sw == 'true')],'id','=',data::$get->id);
    print '<i class="fa fa-check text-green"></i>';
}
function stmr_delete() {
    // TODO rework // service delete
    $tmp = db::$db->select('*','mobile_robot','id','=',data::$get->id)->first();
    if (!$tmp) create_error('Robot Not Found');
    $info = data::decode($tmp['info']);
    if (!data::$get->sim || !data::$get->service) create_error('Missing data');
    $sim = data::$get->sim ; $service = data::$get->service;
    unset($info['services'][$sim][$service]);
    if (!count($info['services'][$sim])) unset($info['services'][$sim]);
    $up = ['info'=>data::encode($info)];
    if (!$tmp['is_busy'] || $tmp['is_busy'] == '-1' ) {
        $up['is_busy'] = count($info['services']) ? '0' : '-1';

    }
    db::$db->update('mobile_robot',$up,'id','=',data::$get->id);
    print style::success_gritter('تم الحذف بنجاح',false);
    print '<script>
$("#tr_for_'.data::$get->area.'").remove();
</script>';
    die();
}
function stmr_save_general() {
    // TODO rework
    $tmp = db::$db->select('*','mobile_robot','id','=',data::$get->id)->first();
    if (!$tmp) create_error('Robot Not Found');
    $info = data::decode($tmp['info']);
    $info['sim_1_name'] = data::$get->sim_1_name;
    $info['sim_2_name'] = data::$get->sim_2_name;
    $info['sim_1_code'] = data::$get->sim_1_code;
    $info['sim_2_code'] = data::$get->sim_2_code;
    $info['sim_1_phone'] = data::$get->sim_1_phone;
    $info['sim_2_phone'] = data::$get->sim_2_phone;
    $info['sim_1_sn'] = data::$get->sim_1_sn;
    $info['sim_2_sn'] = data::$get->sim_2_sn;
    db::$db->update('mobile_robot',['info'=>data::encode($info)],'id','=',data::$get->id);
    print style::success_gritter('تم التعديل بنجاح');
    refresh(true,1000);
    die();
}
function stmr_add() {
    // TODO rework
    if (!in_array(data::$get->sim_select,[1,2]) ) {
        print style::error_gritter('يجب اختيار سيم',false);
        print '<script>$("#sim_select").focus();</script>';
        die();
    }
    if (!data::$get->service_select) {
        print style::error_gritter('يجب اختيار ميزة أو أكثر',false);
        die();
    }
    if (!data::$get->city)data::$get->city = 'ALL';
    $tmp = db::$db->select('*','mobile_robot','id','=',data::$get->id)->first();
    if (!$tmp) create_error('Robot Not Found');

    $info = data::decode($tmp['info']);
    $services = explode(',',data::$get->service_select);
    $cities =  explode(',',data::$get->city);
    foreach ($cities as $city) {
        if ($city == 'ALL') {
            unset($cities);
            $cities[0] = 'ALL';
            break;
        }
    }
    $sim = data::$get->sim_select;
    foreach ($services as $service) {
        $info['services'][$sim][$service]['active'] = 1;
        $info['services'][$sim][$service]['min_balance'] = data::$get->min_balance;
        $info['services'][$sim][$service]['is_auto'] = data::$get->is_auto;
        $info['services'][$sim][$service]['city'] = $cities;
    }
    //dumb($info);
    //die();
    $up = ['info'=>data::encode($info)];
    if (!$tmp['is_busy'] || $tmp['is_busy'] == '-1' ) {
        $up['is_busy'] = count($info['services']) ? '0' : '-1';
    }
    db::$db->update('mobile_robot',$up,'id','=',data::$get->id);
    print style::success_gritter('تم التعديل بنجاح ، الرجاء الانتظار');
    print '<script>
edit_robot("'.data::$get->id.'");
</script>';


}