<?php
if (!allowed('administration') ) {
    redirect_to_index();
}
data::$get->page_title = 'روبوت الكازية';
data::$get->hook = 'mobile_robots';
$html = new style('admin_mobile_robots');
$tmp = db::$db->select('*','robots');
prepare_robots_data($tmp);
$html->fill_table('robot_table',$tmp);
$codes = file_get_contents('./data/mobile_codes.code');
$codes = data::decode($codes);
if (!$codes) $codes[0] = 0;
$html->fill($codes);
job::$body = $html;
function prepare_robots_data($robots) {
    foreach ($robots as $key => $robot) {

    }
    foreach ($data as $key => $datum) {
        $info = data::decode($datum['info']);
        $info_html = '<i class="fa fa-sim-card text-'.($info['sim1'] ? 'green' :'red' ).'">1</i> '.$info['sim_1_name']
        .'<br>'.'<i class="fa fa-sim-card text-'.($info['sim2'] ? 'green' :'red' ).'">2</i> '.$info['sim_2_name'];
       // $stat_info = $datum['stat_info'];
        $battery = $info['charging'] ? '<i class="fa fa-bolt text-orange"></i> ' : '' ;
        $battery .= $info['battery'];
        $bat_level = str_replace('%','',(string) $info['battery']);

        if ($bat_level >= 50) $data->{$key}['battery_color'] = 'green';
        else if ($bat_level >= 30) $data->{$key}['battery_color'] = 'orange';
        else $data->{$key}['battery_color'] = 'red dance';

        $data->{$key}['stat_html'] = ($info['is_ready'] ? '' : 'غير ') . 'جاهزة - '; // a switch in settings for each robot

        $data->{$key}['stat_html'] .= phrases::$mobile_robot_stats[$datum['stat']];

        if ($info['bill_id']) $data->{$key}['stat_html'] .= '<br>الطلب : '.$info['bill_id'];

        if ($datum['disabled']) {
            $data->{$key}['stat_html'] .= '<br>معطلة';
            //stop-circle fa-play-circle
            $data->{$key}['play_stop'] = 'play-circle';
            $data->{$key}['dis_title'] = 'تفعيل';
            $data->{$key}['to_state'] = 'en';
        } else {
            $data->{$key}['play_stop'] = 'stop-circle';
            $data->{$key}['dis_title'] = 'تعطيل';
            $data->{$key}['to_state'] = 'di';
        }
        $data->{$key}['info_html'] = $info_html;
        $data->{$key}['battery'] = $battery;
        $data->{$key}['last_ussd'] = $info['last_ussd'];
    }
}