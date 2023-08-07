<?php
if (!member::check())redirect_to_index();


if (!data::$get->all[1])
{
    mark_active('view');
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
            mark_active('view/todays');
            $fromthismorning = new DateTime();
            $fromthismorning->setTime(0,0,0);
            $tothisnight = new DateTime();
            $tothisnight->setTime(23,59,59);
            if (data::$get->all[2] && is_numeric(data::$get->all[2]) && is_numeric(data::$get->all[3]) && is_numeric(data::$get->all[4]))
            {
                try {
                    $fromthismorning->setDate(data::$get->all[4],data::$get->all[3],data::$get->all[2]);
                    $fromthismorning->setTime(0,0,0);
                    $tothisnight->setDate(data::$get->all[4],data::$get->all[3],data::$get->all[2]);
                    $tothisnight->setTime(23,59,59);
                } catch (Throwable $anyone)
                {
                    $fromthismorning = new DateTime();
                    $fromthismorning->setTime(0,0,0);
                    $tothisnight = new DateTime();
                    $tothisnight->setTime(23,59,59);
                }

            }

            data::$get->page_title = 'تقرير اليوم';
            data::$get->page_name = '<b>'.'تقرير اليوم';
            $sql = 'SELECT lml.* ,
mtt.stat , mtt.device , mtt.type , mtt.sn , mtt.msn , mtt.enter_date ,
ctt.name as customer_name , ctt.phone  as customer_phone
FROM `last_m_log` lml 
LEFT join  `maintenance` mtt ON lml.m_id = mtt.id
LEFT JOIN `customers` ctt ON mtt.customer = ctt.id
WHERE log_enter_date >= \''.$fromthismorning->format(config::$get->storedatetime).'\' AND log_enter_date <= \''.$tothisnight->format(config::$get->storedatetime).'\' ;';
            $workingOn = db::$db->adv_select('','',$sql);
            $sql = 'SELECT 
 mtt.stat , mtt.device , mtt.type , mtt.sn , mtt.msn , mtt.enter_date , mtt.id as m_id ,
 ctt.name as customer_name , ctt.phone  as customer_phone
 FROM `maintenance` mtt 
LEFT JOIN `customers` ctt ON mtt.customer = ctt.id
where stat = 0 and enter_date >= \''.$fromthismorning->format(config::$get->storedatetime).'\' AND enter_date <= \''.$tothisnight->format(config::$get->storedatetime).'\';';
            $newtoday = db::$db->adv_select('','',$sql);
            $newtoday->push($workingOn);

            foreach ($newtoday as $key => $item) {
                $newtoday->{$key}['stat_html'] = phrases::$stat_name[$item['stat']];
                 ($item['sn'] && $item['msn'] && $newtoday->{$key}['snsep'] = '/') || $newtoday->{$key}['snsep'] = '' ;
            }
            //dd($newtoday);
            data::$get->page_name .= ' </b>'.$fromthismorning->format(config::$get->nlongdate)." ( " . ( $newtoday->count() > 0 ? $newtoday->count(): "لا يوجد"  ) . " )";
            $main = new style('maintenance_m');
            $main->part(1);
            $main->fill_table('working_on_table',$newtoday,true);
            break;
        case 'weeks':
            mark_active('view/weeks');
            $fromthismorning = new DateTime();
            $fromthismorning->setTime(0,0,0);
            $oneWeek = new DateInterval('P7D');
            $fromthismorning->sub($oneWeek);
            data::$get->page_title = 'تقرير الاسبوع';
            data::$get->page_name = '<b>'.'تقرير العمل من ';
            $sql = 'SELECT lml.* ,
mtt.stat , mtt.device , mtt.type , mtt.sn , mtt.msn , mtt.enter_date ,
ctt.name as customer_name , ctt.phone  as customer_phone
FROM `last_m_log` lml 
LEFT join  `maintenance` mtt ON lml.m_id = mtt.id
LEFT JOIN `customers` ctt ON mtt.customer = ctt.id
WHERE log_enter_date >= \''.$fromthismorning->format(config::$get->storedatetime).'\';';
            $workingOn = db::$db->adv_select('','',$sql);
            $sql = 'SELECT 
 mtt.stat , mtt.device , mtt.type , mtt.sn , mtt.msn , mtt.enter_date , mtt.id as m_id ,
 ctt.name as customer_name , ctt.phone  as customer_phone
 FROM `maintenance` mtt 
LEFT JOIN `customers` ctt ON mtt.customer = ctt.id
where stat = 0 and enter_date >= \''.$fromthismorning->format(config::$get->storedatetime).'\';';
            $newtoday = db::$db->adv_select('','',$sql);
            $newtoday->push($workingOn);
            foreach ($newtoday as $key => $item) {
                $newtoday->{$key}['stat_html'] = phrases::$stat_name[$item['stat']];
                ($item['sn'] && $item['msn'] && $newtoday->{$key}['snsep'] = '/') || $newtoday->{$key}['snsep'] = '' ;
            }
            //dd($newtoday);
            data::$get->page_name .= ' </b>'.$fromthismorning->format(config::$get->nlongdate)." ( " . ( $newtoday->count() > 0 ? $newtoday->count(): "لا يوجد"  ) . " )";
            $main = new style('maintenance_m');
            $main->part(1);
            $main->fill_table('working_on_table',$newtoday,true);
            break;
        default:
            redirect(true,'','view');
            die();
    }
} else
{
    mark_active('view');
    data::$get->page_title = 'عرض صيانة '.data::$get->all[1];
    $main = new style('maintenance_view');
    $html = $main->get_part(3);
    $add_report_html = $main->get_part(5);
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
        $add_report_html = '';
    } else {
        $add_report_html->fill($info);
        $html = '';
    }
    $main->str_replace('<!-- ADD_REPORT -->',$html.''.$add_report_html);
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