<?php
if (!member::check())redirect_to_index();
mark_active('view');
if (!data::$get->all[1])
{
    data::$get->page_title = 'كل الصيانات';
    $main = new style('maintenance_mp');
    $workingOn = db::$db->select('*','stm','stat','IN','(0,1,4)','id','DESC');
    foreach ($workingOn as $key => $item) {
        $workingOn->{$key}['stat_html'] = phrases::$stat_name[$item['stat']];
        ($item['sn'] && $item['msn'] && $workingOn->{$key}['snsep'] = '/') || $workingOn->{$key}['snsep'] = '' ;
    }
    $duabi = db::$db->select('*','stm','stat','=','5','id','DESC');
    foreach ($duabi as $key => $item) {
        $duabi->{$key}['stat_html'] = phrases::$stat_name[$item['stat']];
        ($item['sn'] && $item['msn'] && $duabi->{$key}['snsep'] = '/') || $duabi->{$key}['snsep'] = '' ;
    }
    $onhold = db::$db->select('*','stm','stat','=','6','id','DESC');
    foreach ($onhold as $key => $item) {
        $onhold->{$key}['stat_html'] = phrases::$stat_name[$item['stat']];
        ($item['sn'] && $item['msn'] && $onhold->{$key}['snsep'] = '/') || $onhold->{$key}['snsep'] = '' ;
    }
    $ready = db::$db->select('*','stm','stat','=','2','id','DESC');
    foreach ($ready as $key => $item) {
        $ready->{$key}['stat_html'] = phrases::$stat_name[$item['stat']];
        ($item['sn'] && $item['msn'] && $ready->{$key}['snsep'] = '/') || $ready->{$key}['snsep'] = '' ;
    }
    data::$get->working_on_total = "( " . ( $workingOn->count() > 0 ? $workingOn->count(): "لا يوجد"  ) . " )";
    data::$get->onhold_total = "( " . ( $onhold->count() > 0 ? $onhold->count(): "لا يوجد"  ) . " )";//$onhold->count();
    data::$get->ready_total = "( " . ( $ready->count() > 0 ? $ready->count(): "لا يوجد"  ) . " )";// $ready->count();
    data::$get->dubai_total = "( " . ( $duabi->count() > 0 ? $duabi->count(): "لا يوجد"  ) . " )";// $duabi->count();
    //$last_reports = db::$db->select('')
    $main->fill_table('working_on_table',$workingOn,true)
        ->fill_table('dubai_table',$duabi,true)
        ->fill_table('onhold_table',$onhold,true)
        ->fill_table('ready_table',$ready,true);

} else if (! is_numeric(data::$get->all[1]))
{
    switch (data::$get->all[1])
    {
        case 'todays':
            data::$get->page_title = 'Today\'s';
            $workingOn = db::$db->select('*','stm','stat','IN','(0,1,4)','id','DESC');
            foreach ($workingOn as $key => $item) {
                $workingOn->{$key}['stat_html'] = phrases::$stat_name[$item['stat']];
                 ($item['sn'] && $item['msn'] && $workingOn->{$key}['snsep'] = '/') || $workingOn->{$key}['snsep'] = '' ;
            }
            data::$get->working_on_total = "( " . ( $workingOn->count() > 0 ? $workingOn->count(): "لا يوجد"  ) . " )";
            $main = new style('maintenance_m');
            $main->fill_table('working_on_table',$workingOn,true);
            break;
        default:
            redirect(true,'','view');
            die();
    }
} else
{
    data::$get->page_title = 'عرض صيانة '.data::$get->all[1];
    $main = new style('maintenance_view');
    $html = $main->get_part(3);
    $prev_html = $main->get_part(4);
    $bill = new maintenance(data::$get->all[1]);
    if ($bill->hasError())
    {
        create_error('In a Normal Universe Like OURS You Cannot View Something Does Not Exist','404');
    }
    $info = $bill->getFillInfo();
    $main->part(1)->fill($info,null,true);
    foreach ($info['btnsToRemove'] as $item) {
        if ($item) $main->del_btn($item);
    }
    if ($bill->canAddReport())
    {
        $html->fill($info);
    } else {
        $html = '';
    }
    $main->str_replace('<!-- ADD_REPORT -->',$html.'');
    $old_reports = $bill->getAllReports();
    $main->fill_table('all_reports',$old_reports);
    $prev_bills = $bill->getPrevBills();
//dd($prev_bills);
    $prev_html->fill_table('old_reports',$prev_bills);
    $main->str_replace('<!-- PREV_BILLS -->',$prev_html.'');
//dd($old_reports);

//dd($bill->getPrevBills());
}
job::$body = $main;