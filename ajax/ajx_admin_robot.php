<?php
//dumb(data::$get);
if (!allowed('administration') ) {
    redirect_to_index();
}
switch (data::$get->btn) {
    case 'edit':
        mobile_robot_edit();
        break;
    case 'en_dis':
        mobile_robot_en_dis();
        break;
    case 'delete':
        mobile_robot_delete();
        break;
    case 'save_codes':
        mobile_robot_save_codes();
        break;
    default:
        die('Unknown '.data::$get->btn);
}
function mobile_robot_save_codes() {
    foreach (data::$get as $key => $value) {
        if (strstr($key,'code_')) {
            $codes[$key] = $value;
        }
    }
    $codes = data::encode($codes);
    file_put_contents('./data/mobile_codes.code',$codes);
    print style::success_gritter('تم حفظ التعديلات');
    die();
    //dumb();
    /** $codes = file_get_contents('./data/mobile_codes.code');
    $codes = data::decode($codes); */
}
function mobile_robot_edit() {
    if (!data::$get->id) create_error('Unknown ID');
    $robot = db::$db->select('*','robots','id','=',data::$get->id)->first();
    if (!$robot) create_error('Not Found');
    // robot data prepare
    $html = new style('admin_mobile_robot_edit');
    $info = data::decode($robot['info']);
    foreach ($info as $key => $item) {
        $robot[$key]=$item;
    }
    $services = $robot['services'];
    foreach ($services as $sim_number => $service_list)  {
        unset($item);
        $item['id'] = $robot['id'];
        $item['sim_number'] = '<i class="fa fa-sim-card">'.$sim_number.'</i>';
        $s_name = 'sim_'.$sim_number.'_name';
        $item['sim_name'] = $robot[$s_name];
        $item['sim'] = $sim_number;
        foreach ($service_list as $service_name => $value) {
            $item['service'] = $service_name;
            $item['service_name'] = phrases::$mobile_robot_service_names[$service_name];
            $item['is_auto_html'] = '<i class="fa fa-'.($value['is_auto'] == 'true' ? 'check' : 'times').' text-'.($value['is_auto'] == 'true' ? 'green' : 'red').'"></i>';
            $item['cities_html'] = '<span class="badge badge-info badge-pill">'.implode('</span> <span class="badge badge-info badge-pill">',$value['city']).'</span>';
            $item['min_balance'] = $value['min_balance'];
            $list[] = $item;
        }
    }
    $dis_btn = style::check_box('ready_sw','الروبوت جاهز للعمل','robot_rd_<!-- RID -->',!!$robot['is_ready']);
    $robot['dis_btn_html'] = $dis_btn['checkbox'];
    $robot['dis_btn_label'] = $dis_btn['label'];
    $auto_check = style::check_box('is_auto','تنفيذ تلقائي بدون موافقة موظف','add_service_<!-- RID -->');
    $robot['auto_check_h'] = $auto_check['checkbox'];
    $robot['auto_check_l'] = $auto_check['label'];
    $html->fill_table('service_table',$list,false,null,'لا يوجد')->fill($robot)->create_modal('');
    print $html;
    die();

}
function mobile_robot_delete() {
    if (!data::$get->id) create_error('Unknown ID');
    db::$db->delete('robots','id','=',data::$get->id);
    print style::success_gritter();
    print '<script>$("#mr_row_'.data::$get->id.'").remove();</script>';
    die();
}
function mobile_robot_en_dis() {
    //TODO rework
    if (!data::$get->id) create_error('Unknown ID');

    db::$db->update('robots',['disabled' => (data::$get->to_state == 'en' ? '0':'1' )],'id','=',data::$get->id);
    print style::success_gritter();
    data::$get->to_state = data::$get->to_state == 'en' ? 'di' : 'en';
    print '<script>update_row("'.data::$get->id.'","'.data::$get->to_state.'");</script>';
    die();
}