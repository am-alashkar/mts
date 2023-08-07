<?php
header("Cache-Control: no-cache");
header("Content-Type: text/event-stream");
if (member::$current->id < 1 && config::$get->force_login) die();
$i=0;
$result['values']['0'] = '';
$result['values']['balance'] = +member::$current->balance;
$result['scripts']['0'] = style::variable('fader_fix');
$html = new style();
$html->add_html(date(config::$get->nlongdate) . " " . date('h:i {{A}}'));
$result['html']['server_time'] = $html.'';
$result['html']['server_time_custom'] = date('d-m-Y @ h:i A');

while(data::$get->all[++$i]) {
    $fun = 'hook_func_' . data::$get->all[$i];
    //$result['debug'][] = $fun;
    if (function_exists($fun)) {
        $result = $fun($result,$i);
    }
}
$result = json_encode($result);
flush();
echo "retry: 5000\n";
echo "event: Stater\n";
echo "data: ".$result . "\n\n";
die();

function hook_func_dummy($result,&$i) {
    if (allowed())
    return $result;

}
function hook_func_mobile_robots($result,&$i) {
    $data = db::$db->select('*','robots');
    foreach ($data as $key => $datum) {
        $info = data::decode($datum['info']);
        $info_html = '<i class="fa fa-sim-card text-'.($info['sim1'] ? 'green' :'red' ).'">1</i> '.$info['sim_1_name']
            .'<br>'.'<i class="fa fa-sim-card text-'.($info['sim2'] ? 'green' :'red' ).'">2</i> '.$info['sim_2_name'];
        // $stat_info = $datum['stat_info'];
        $battery = $info['charging'] ? '<i class="fa fa-bolt text-orange"></i> ' : '' ;
        $battery .= $info['battery'];
        $bat_level = str_replace('%','',(string) $info['battery']);
        $result['scripts'][] = 'update_row("'.$datum['id'].'","'.($datum['disabled'] ? 'en':'di').'")';
        $result['html']['mr_last_seen_'.$datum['id']] = (new style())->add_html('<!-- INFO_lastseen|auto -->')->fill($datum).'';
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
        $result['html']['mr_battery_'.$datum['id']] = (new style())->add_html('<!-- INFO_stat_html -->
                                    <br><i class="fa fa-car-battery text-<!-- INFO_battery_color -->"></i> <!-- INFO_battery -->')->fill($data->{$key}).'';
    }
    foreach ($robots as $robot) {
        break;
        unset($info);
        $result['scripts'][] = 'update_row("'.$robot['id'].'","'.($robot['disabled'] ? 'en':'di').'")';
        $result['html']['mr_last_seen_'.$robot['id']] = (new style())->add_html('<!-- INFO_last_seen|auto -->')->fill($robot).'';
        $stat_info = $robot['stat_info'];
        $info['battery'] = $stat_info[2] ? '<i class="fa fa-bolt text-orange"></i> ' : '' ;
        $bat_level = substr($stat_info,3) ;
        $info['battery'] .= $bat_level.'% ';
        if ($bat_level >= 50) $info['battery_color'] = 'green';
        else if ($bat_level >= 30) $info['battery_color'] = 'orange';
        else $info['battery_color'] = 'red dance';
        $info['stat_html'] = ($robot['is_ready'] ? '' : 'غير ') . 'جاهزة - ';
        if ($robot['is_busy'] == -1) {
            // new
            $info['stat_html'] .= ' لم يتم الاعداد ';
        }  elseif ($robot['is_busy'] == -2) {
            // manual control
            $info['stat_html'] .= ' وضع التحكم اليدوي ';
        }  elseif ($robot['is_busy'] == -3) {
            // manual control
            $info['stat_html'] .= ' الاستعلام عن الرصيد ';
        }  elseif ($robot['is_busy'] == -4) {
            // manual control
            $info['stat_html'] .= ' تقوم بارسال الرد إلى الموقع ';
        } elseif ($robot['is_busy'] > 0 ) {
            // doing bill
            $info['stat_html'] .= ' تنفذ الطلب : ' . $robot['is_busy'];
        } elseif ($robot['is_busy'] < -3) {
            $info['stat_html'] .= '<span class="text-red">يوجد خطأ</span>';

        } elseif ($robot['is_ready'] && !$robot['disabled'])  {
            // ready and waiting for bill
            $info['stat_html'] .= 'بانتظار الطلبات';
        }
        if ($robot['disabled']) {
            $info['stat_html'] .= '<br>معطلة';
        }
        $result['html']['mr_balances_'.$robot['id']] = '';
        $b_info = data::decode($robot['info'])['balance'];
        foreach ($b_info as $item) {
            $bal[] = $item['name'].' : '.$item['value'];
        }
        if ($bal) {
            $result['html']['mr_balances_'.$robot['id']] = implode('<br>',$bal);
        }
        $result['html']['mr_battery_'.$robot['id']] = (new style())->add_html('<!-- INFO_stat_html -->
<br><i class="fa fa-car-battery text-<!-- INFO_battery_color -->"></i> <!-- INFO_battery -->')->fill($info).'';
    }
    return $result;
}