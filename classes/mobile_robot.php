<?php


class mobile_robot
{
    /** @var int $id Robot ID in db
     * @var string $guid Robot guid
     *  @var string $smsg Server Message Displayed on mobile
     *  @var string $call Number to call from * and ends with #
     *  @var string $run Method name to run inside the app
     * @var int $sim Sim number . 1 or 2
     * @var array $ans Answer Array to app
     */
    private $id,$smsg,$call,$run,$sim,$guid,$ans,$bat_level ,$sim1,$sim2,$charger,$rbt_stat,$debug,$job_id,$bill_id,$req_job;
    public $info,$is_busy,$is_ready,$stat_info,$last_seen,$disabled;
    static $rb;
    function __construct()
    {
        if ($_SERVER['HTTP_USER_AGENT'] != 'UBXv9') {
            $this->error('Incompatibly Error ',1);
        }
        if (data::$get->d != '1.EOF') {
            $this->error('INCOMPLETE TRANSFER',1);
        }
        $this->stat_info = $info = data::$get->info;
        $this->sim1 = $info[0];
        $this->sim2 = $info[1];
        $this->charger = $info[2];
        $this->rbt_stat = data::$get->tag;
        $this->bat_level = substr($info,3);
        $this->last_seen = date(config::$get->storedatetime);
        $this->bill_id = data::$get->bill_id;
        $this->job_id = data::$get->job_id;
        $this->req_job = data::$get->req_job;
        if (!data::$get->id) {
            $this->register();
        } else {
            $this->check();
        }

        self::$rb = $this;
    }
    public function add_to_debug($txt) {
        $this->debug .= "\n".$txt;
    }
    public function balance() {

    }
    public function switch_to_error_stat($error_id = 10) {
        db::$db->update('mobile_robot',['is_busy'=> -abs($error_id)],'id','=',$this->id);
//        die(db::$db->get_last_error());
    }
    public function set_doing_bill($bill_id = 0) {
        if (!$bill_id) $bill_id = -3;
        else $bill_id = abs($bill_id);
        db::$db->update('mobile_robots',['is_busy'=> $bill_id],'id','=',$this->id);
    }
    public function has_error() {
        return $this->is_busy < -4;
    }
    private function check() {
        if (!data::$get->gid || !data::$get->id) $this->error('Android Error');
        $rbt = db::$db->select('*','mobile_robot','id','=',data::$get->id)->first();
        if (!$rbt || $rbt['guid'] != data::$get->gid) return $this->clean('Robot Deleted');
        $this->id = $rbt['id'];
        $this->is_ready = $rbt['is_ready'];
        $this->is_busy = $rbt['is_busy'];
        //$info = data::decode($rbt['info']);
        //$this->sim1 = $info['sim1'];
        //$this->sim2 = $info['sim2'];
        $this->info = $rbt['info'];
        $this->last_seen = date(config::$get->storedatetime);
        $this->disabled = $rbt['disabled'];
        $this->save();
    }
    public function ready_to_call() {
        if ($this->run) return false;
        if ($this->is_busy == -1) {
            // new
            $this->smsg = ' لم يتم الاعداد ';
            return false;
        }  elseif ($this->is_busy == -2) {
            // manual control
            $this->smsg = ' وضع التحكم اليدوي ';
            return false;
        } elseif ($this->is_busy > 0) {
            // doing bill
            $this->smsg = ' تنفذ الطلب : ' . $this->is_busy;
            return false;
        } elseif ($this->is_busy == -3) {
            // doing bill
            $this->smsg = ' الكشف عن الرصيد ';
            return false;
        } elseif ($this->is_busy == -4) {
            $this->smsg = ' ارسال البيانات ';
            return false;
        } elseif ($this->is_busy < -4) {
            $this->smsg = 'يوجد خطأ الرجاء مراجعة الموقع';
            return false;
        } elseif ($this->disabled)  {
            // disabled
            $this->smsg = 'معطلة من الموقع';
            return false;
        } elseif (!$this->is_ready) {
            // not ready
            $this->smsg = 'غير جاهز';
            return false;
        }
        $info = data::decode($this->info);
        if (!$info['sim_1_name'] || !$info['sim_1_sn'] || !$info['sim_1_code'] || !$info['sim_1_phone']) $sim1 = 0;
        else $sim1 = 1;
        if (!$info['sim_2_name'] || !$info['sim_2_sn'] || !$info['sim_2_code'] || !$info['sim_2_phone']) $sim2 = 0;
        else $sim2 = 1;
        if (!($sim2+$sim1)) return false;
        return true;
        // find a job
        //
    }
    public function get_rbt_stat() {
        return $this->rbt_stat;
    }
    public function add_job($id ,$sim , $code,$is_bill = true) {
        // for the return script .. is the same as always because it is defined by the received USSD message ..
        // Anyway a robot stat will determine something .. and the message will choose a function.
        // EX: robot is waiting for balance .. a wrong ussd received .. or nothing at all ..
        // A return script will handle wrong messages .. a timeout script ( by robot stat ) will handle timeout
        if ($this->rbt_stat) $this->error('No job can be added while Robot is not in clear stat. Please check your code');
        // code is filled with bill data .. if it is a bill .. now replace sim data
        $snk = 'sim_'.$sim.'_sn';
        $phk = 'sim_'.$sim.'_phone';
        $cdk = 'sim_'.$sim.'_code';
        $code = str_replace('c',$this->$cdk,$code);
        $code = str_replace('r',$this->$phk,$code);
        $code = str_replace('s',$this->$snk,$code);
        $this->ans['code'] = $code;
        $this->ans['sim'] = $sim;
        $this->ans['is_bill'] = +$is_bill;
        $this->ans['bid'] = $id;
        $this->run = 'register_call';
        return $this;
    }

    public function confirm($job_id = 0) {
        $this->run = 'confirm';
        $this->ans['job_id'] = $job_id;
        return $this;
    }
    public function save() {
        if ($this->id) {
            if(!is_array($this->info)) $this->info = data::decode($this->info);
            $this->info['sim1'] = $this->sim1;
            $this->info['sim2'] = $this->sim2;
            $this->info = data::encode($this->info);
            db::$db->update('mobile_robot', $this, 'id', '=', $this->id);
            //$this->info = data::decode($this->info);
        }
    }
    private function error($error , $clear = false) {
        if (!$clear) $stat = "\n".'Sim1 '.($this->sim1 ? 'OK':'None') .' - Sim2 '.($this->sim2 ? "OK" : 'None')."\n".'Charging '.($this->charger ? 'YES':'NO').' - Battery '. $this->bat_level.'%';
        print json_encode(['Error'=>$error.$stat]);
        die();
    }
    public function soft_clean($reason = "") {
        $this->run = 'soft_clean';
        $this->smsg = 'SC '.$reason;
        $this->is_ready = 0;
        $this->is_busy = 0;
        $this->save();
        return $this;
    }
    private function clean($reason = "") {
        $this->run = 'clean';
        $this->ans['id_to_save'] = 0;
        $this->ans['guid'] = 0;
        $this->smsg = 'CLEAN '.$reason;
        $this->is_ready = 0;
        return $this;
    }
    private function register() {
        if ($this->req_job == 'step_2') return $this->register_step2();
        $this->run = 'register';
        $this->smsg = 'تسجيل';
        return $this;
    }
    private function register_step2() {
        $in = new result();
        $guid = gen_uuid();
        //if (!$guid) $this->error('No GUID');
        $in->guid = $guid;//data::decode($guid);
        $info['sim1'] = $this->sim1;
        $info['sim2'] = $this->sim2;
        $in->info = data::encode($info);
        $in->is_busy = -1;
        $in->is_ready = 0;
        $in->disabled = 0;
        $in->stat_info = data::$get->info;
        //$in->sim_1_name = $in->sim_2_name = "";
        $in->type = 'mobile_robot';
        $this->id = db::$db->insert('mobile_robot',$in);
//        var_dump(db::$db->get_last_error());
//        die();
        if (!$this->id) {
            $this->run = '';
            //$this->smsg = 'Error in Server Side - 63';
            $this->error('Error : MR68 Database Access Failed');
            //return $this;
        }
        $this->guid = $guid;
        $this->run = 'register';
        $this->ans['id_to_save'] = $this->id;
        $this->ans['guid'] = $this->guid;
        $this->smsg = 'تسجيل';
        return $this;
    }
     function get_array()
    {
        $ans = $this->ans;
        $ans['smsg'] = $this->smsg;//.' TAG = '.$this->tag;
        $ans['smsg'] .= $this->debug;//"\n".'Debug:'
        $ans['id'] = $this->id;
        $ans['run'] = $this->run ? $this->run : 'nothing';//.rand(100,999);

        return $ans;
    }
    function print_debug_data() {
        print $this->rbt_stat;
        die();
    }

    public function get_unsent()
    {
        $this->run = 'get_unsent';
        $this->add_to_debug('sending get unsent');
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJobId()
    {
        return $this->job_id;
    }

    /**
     * @return mixed
     */
    public function getBillId()
    {
        return $this->bill_id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    public function get_hash_id() {
        return $this->id.'-'.$this->bill_id.'-'.$this->job_id;
    }
}