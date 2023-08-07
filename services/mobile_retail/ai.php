<?php
/** Mobile Retail AI */
if (data::$get->all[1] != 'mobile_retail') {
    create_error("Wrong API");
}
/**
 * @var json_out $out
 * Already initialized from previous file
 */
/**
 * New method :
 * Load robot.
 * if no SID .. ( fail as in case of deleted Robot will cause no SID )
 *          is there confirm register request ?
 *                  yes -> create new and send SID and ID
 *                  no  -> send register signal
 * else
 *          stat from DB
 *              ready -> find a job
 *              disabled -> wait ( new robots are disabled _ no service robots are disabled _ too many errors are disabled _ manual control )
 *              doing a job -> wait ( add a counter in db so in case too much you must reset counter , add error , reset )
 *
 * data always sent from robot .. in headers:
 *          HTTP_BINFO battery info xxx:z or xx:z or x:z or :z, where x is bat level , z is charger type;
 *          HTTP_KID robot id
 *          HTTP_GID robot guid
 *          HTTP_ISON G.isOn
 *          HTTP_ACCSER G.accService
 *
 *
 * command from robot :
 *          ready -> ready to do a job
 *          add response -> send response text and job_id and buttons and inputs if not auto cancel
 *          busy -> doing a job
 *          confirm added job -> send job id back and something about the call and sim ( CRC or other )
 *
 * command to robot :
 *          nothing -> do nothing .
 *          clean -> remove busy stat and switch to ready stat
 *          clear -> remove every thing.
 *          add ( call_number , job_id , sim_number ) -> add a job - call number - job id - sim number
 *          confirm ( job_id ) -> execute job by job id .
 *          are you a robot ? -> send confirm register
 *          register (sid , id) -> store SID and ID
 *          confirm receive ( job_id ) -> delete job from Mobile DB
 *
 *
 * Data always sent:
 *          sim1 name
 *          sim2 name
 *          Server message
 *          auto cancel -> true or false ( false in case manual control )
 *          command to robot -> from previous table
 *
 *
 */
$robot = new mobile_retail_robot();
if (!$robot->check())
{
    if ($robot->getCommand() == 'register')
    {
        $robot->createNewRobot();
        $out->run = 'register';
        $out->guid = $robot->getRegisterInfo()['guid'];
        $out->id_to_save = $robot->getRegisterInfo()['id'];
    } else
    {
        $out->run = 'send_register';
    }
} else {
    // check stat from db
    /** if the robot is in working mode (server side info) then check for jobs
     * else a robot could be disabled .. just display a message
     * or the robot will be in manual mode then send command if there is one or just receive the last USSD sent.
     *
     */
    //file_put_contents('Robot_log.txt',$robot->getCommand()."\n",FILE_APPEND);
    //die('E');
    if ($robot->getCommand() == '')
    {
        // find a job

    } else
    {
        // nothing

    }
}

/**
 *
$out->run = "set_message";
$out->msg = "الرجاء شحن البطارية لانها ممتلئة
";
new mobile_robot();

/**
 * Depending on robot stat sent by post and robot stat from class
 *      We have to decide what to do
 * if not Robot stat then first check for balances
 *  if some balances need to check then check ( add a check job )
 *      else see active balances and find a suitable job
 *          if there is a job then execute sequence .
 *                   else nothing
 * ELSE IF robot stat .. that's another story.

$rbt_stat = mobile_robot::$rb->get_rbt_stat();
$robot = mobile_robot::$rb;
if (!$rbt_stat) {
    $robot->add_to_debug('No RBT STAT');
    if ($robot->ready_to_call()) {
        $info = data::decode($robot->info);
        $services = $info['services'];
        $sim_1_services = $services['1'];
        $sim_2_services = $services['2'];
        foreach ($sim_1_services as $type => $array) {
            if (!$array['balance_ready']) break;
        }
        if (!$array['balance_ready']) {
            $robot->add_to_debug('Sim1 BNR '.$type);
            // check if there is a code for this .. or set balance to ready and active to 0
            $code = load_codes_for($type,'balance');
            if (!$code) {
                $info['services']['1'][$type]['active']  = 0;
                $info['services']['1'][$type]['balance_ready']  = 1;
                $info['services']['1'][$type]['balance']  = 0;
                $info = data::encode($info);
                $robot->info = $info;
                $robot->save();
            } else {
                // add a job
                $robot->is_busy = -3;
                $robot->save();
                $robot->add_job(0,1,$code,false);
                //$robot->add_to_debug('Add BAL_JOB');
            }
        } else  {
            foreach ($sim_2_services as $type => $array) {
                if (!$array['balance_ready']) break;
            }
            if (!$array['balance_ready']) {
                $robot->add_to_debug('Sim2 BNR ' . $type);
                $code = load_codes_for($type,'balance');
                if (!$code) {
                    $info['services']['2'][$type]['active']  = 0;
                    $info['services']['2'][$type]['balance_ready']  = 1;
                    $info['services']['2'][$type]['balance']  = 0;
                    $info = data::encode($info);
                    $robot->info = $info;
                    $robot->save();
                } else {
                    // add a job
                    $robot->is_busy = -3;
                    $robot->save();
                    $robot->add_job(0,2,$code,false);
                    //$robot->add_to_debug('Add BAL_JOB');
                }
            } else {
                $robot->add_to_debug('Find_JOB');
                // find a bill
                // take it
                // send signal
            }
        }

    } else {
        $robot->add_to_debug('Not Ready to call');
        $robot->add_to_debug('IB"'.$robot->is_busy.'"');

    }
} else {
    //$robot->add_to_debug('RPT IS HERE');
    if ($robot->is_busy == '0') {
        // if is_busy == 0 that mean user reset the robot .. and the robot is not in internal ready stat ( G.toweb_msg != "" )
        $robot->soft_clean();
    } else {
        $robot->add_to_debug('RPT:'.$rbt_stat);
        $f_name = 'mrr_fun_'.phrases::$mobile_robot_functions[strtolower(trim((string) explode(':',$rbt_stat)[0]))];
        $robot->add_to_debug('Fn:'.$f_name);
        if (!function_exists($f_name)) {
            $robot->switch_to_error_stat();
            $robot->add_to_debug('SERVER Error');
        } elseif (rbt_continue($robot)) {
            //$robot->add_to_debug('rbt_continue');
            $f_name($robot);
            //die('OPS');
        } else {
            //$robot->add_to_debug('!rbt_continue');
        }
    }

}
$out->join($robot->get_array());
function mrr_fun_confirmed(mobile_robot $robot) {
    // too many of this means no call function done

}
function mrr_fun_called(mobile_robot $robot) {
    // too many of this means call function done but no ussd received

}
function mrr_fun_have_unsent(mobile_robot $robot) {
    // tell the robot to send data
    $robot->get_unsent();
    $robot->is_busy = -4; // sending data;
    $robot->save();

}
function mrr_fun_receive_ans(mobile_robot $robot) {
    $robot->add_to_debug('data reading');
    $ans = data::$get->data;
    $ans = base64_decode($ans);
    $ans = implode('',explode(',',$ans));
    process_message($ans,$robot);
    $ans.= $robot->getJobId().$robot->getBillId();
    var_dump($ans);

    die();
}
function mrr_fun_confirm_is_receive(mobile_robot $robot) {
    $robot->add_to_debug($robot->getId().'-'.$robot->getJobId().'-'.$robot->getBillId());
    $robot->get_unsent();
}

function mrr_fun_added_job(mobile_robot $robot) {
    // if it is a bill : check it first then confirm or clear
    // if it is not bill just confirm
    //die('Err');
    $a = explode(':',$robot->get_rbt_stat());
    $robot->add_to_debug('Confirming '.$a[2]);
    $job_id = $a[1];
    if ($job_id === '0') {
        $robot->confirm($a[2]);
    } else {

    }
}
function mrr_fun_(mobile_robot $robot) {
    $robot->add_to_debug('UN:'.$robot->get_rbt_stat());
}
function load_codes_for($service,$type) {
    $code = data::decode(file_get_contents('./data/mobile_codes.code'));
    $key = 'code_'.$service.'_'.$type;
    return $code[$key];
}
function rbt_continue($robot) {
    if ($robot->has_error()) {
        $robot->add_to_debug('ROBOT Error');
        return false;
    }
    return true;
}
function process_message($msg , mobile_robot $robot) {
    $idh = $robot->get_hash_id();
    //$msg = db::$db->select()
}
 *
 * */