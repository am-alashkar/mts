<?php


class mobile_retail_robot extends ubx_robot
{
    private $sid,$binfo;
    private $accser,$autoCancel,$debugMode;
    private $isOn;

    public function __construct()
    {

        $this->sid = $_SERVER['HTTP_GID'];
        $id = $_SERVER['HTTP_KID'];
        $this->binfo = $_SERVER['HTTP_BINFO'];
        $this->accser = $_SERVER['HTTP_ACCSER'];
        $this->isOn = $_SERVER['HTTP_ISON'];
        $this->autoCancel = $_SERVER['HTTP_AC'];
        $this->debugMode = $_SERVER['HTTP_DM'];
        //$this->command = data::$get->command;
        parent::__construct($id);
        $this->script = 'mobile';
        $this->decode();
        $this->setSeen();

    }
    private function setSeen() : bool {
        if ($this->check())
        {
            $up['lastseen'] = date(config::$get->storedatetime);
            $up['info'] = $this->info;
            $up['info']['ison'] = $this->isOn;
            $up['info']['accser'] = $this->accser;
            $up['info']['battery'] = explode(':',$this->binfo)[0].'%';
            $up['info']['charging'] = explode(':',$this->binfo)[1] ? '⚡' : '';
            // stat update
            if ($up['info']['ison'] != 'ok') $up['stat'] = '6'; // turned off from mobile
            else if ($up['info']['accser'] != 'ok') $up['stat'] = '8'; // accessibility service is off

            $up['info'] = data::encode($up['info']);
            return db::$db->update('robots',$up,'id','=',$this->id);
        }
        return false;
    }
    private function decode(){
        if (!is_array($this->info)) $this->info = data::decode($this->info);
    }
    private function encode() {
        if (is_array($this->info)) $this->info = data::encode($this->info);
    }
    public function check(): bool
    {

        if ($this->script != 'mobile') return false;
        if ($this->info['sid'] != $this->sid) return false;
        if (!$this->id) return false;
        return true;
    }
    public function createNewRobot()
    {
        $this->sid = gen_uuid();
        $this->info['sid'] = $this->sid;
        $this->encode();
        $this->id = db::$db->insert('robots',$this);
        return $this->id;
    }
    function getRegisterInfo(){
        return ['id'=>$this->id,'guid'=>$this->sid];
    }

}