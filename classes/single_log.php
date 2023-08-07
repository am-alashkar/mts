<?php


class single_log
{
    private ?int $id;
    public ?int $m_id,$user_id;
    public ?int $new_stat = 0,$prev_stat = 0;
    public ?string $log_enter_date = "",$report = "";
    private string $error = "";
    public function buildFromInput() : single_log
    {
        $this->report = data::$get->new_report;
        $this->log_enter_date = date(config::$get->storedatetime);
        $this->m_id = data::$get->id;
        $this->user_id = member::get_id();
        $this->new_stat = data::$get->new_stat;
        //$this->prev_stat =
        return $this;
    }
    public function insert($stat = false) : bool
    {
        if ($this->user_id < 1) return false;
        if ($this->m_id < 1) return false;
        $this->id = db::$db->insert('m_log',$this);
        if (!$this->id)
        {
            $this->error = db::$db->get_last_error();
            return false;
        }
        if ($stat) return true;
        return db::$db->update('maintenance',['stat'=>$this->new_stat],'id','=',$this->m_id);
    }

}