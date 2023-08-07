<?php

define('ROBOT_STAT_DISABLED',0);
define('ROBOT_STAT_ENABLED',1);
abstract class ubx_robot
{

    public $stat,$script,$info,$lastseen;
    protected int $id;
    protected bool $saved;
    protected string $command;
    function __construct($id = null)
    {
        $this->command = data::$get->command;
        if ($id)
        {
            $rbt = db::$db->select('*','robots','id','=',$id)->first();
            if ($rbt)
            {
                $this->id = $id;
                $this->stat = $rbt['stat'];
                $this->script = $rbt['script'];
                $this->info = data::decode($rbt['info']);
                $this->saved = true;
            }
            else $this->create_new();
        } else $this->create_new();
    }
    protected function create_new() : void {
        $this->id = 0;
        $this->script = '';
        $this->info = [];
        $this->saved = false;
        $this->stat = ROBOT_STAT_DISABLED ;
        $this->lastseen = date(config::$get->storedatetime);
    }
    public function save() : bool
    {
        if (!$this->check_data()) return false;
        if (is_array($this->info)) $this->info = data::encode($this->info);
        if ($this->id) db::$db->update('robots',$this,'id','=',$this->id);
        else $this->id = db::$db->insert('robots',$this);
        if (!is_array($this->info)) $this->info = data::decode($this->info);
        return $this->saved = true;
    }
    protected function check_data() : bool {
        if (!$this->check()) return false;
        return !!$this->script;
    }
    abstract function check() : bool ;
    public function getId(){
        return $this->id;
    }
    public function getCommand() : string {
        return $this->command;
    }
    abstract function createNewRobot();
}