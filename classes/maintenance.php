<?php


class maintenance
{
    public ?string $device = '',$type = '',$enter_date = '',$sn = '',$msn = '',$adds = '',$notes = '',$description = '' , $out_date = '' , $out_notes = '';
    private ?int $id = null;
    public ?int $added_by = null,$customer = null ,$stat = 0 , $out_by = null;
    private ?m_log $log;
    private string $error = '';
    function __construct($id = null)
    {
        if ($id)
        {
            $tmp = db::$db->select('*','maintenance','id','=',$id)->first();
            if (!$tmp)
            {
                $this->error = 'Load Error '.db::$db->get_last_error();
            } else
            {
                $this->log = new m_log($id);
                foreach ($tmp as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
        $this->log = new m_log($this->id);
    }
    public function createNewFromInput() : maintenance
    {
        $this->error = '';
        $this->customer  = db::$db->select('*','customers','id','=',data::$get->customer)->first()['id'];
        if (!$this->customer)
        {
            $this->error = 'يجب اختيار زبون';
        }
        $this->device = data::$get->device;
        if (!$this->device)
        {
            $this->error .= "\n يجب كتابة اسم الجهاز";
        }
        $this->sn = data::$get->sn;
        if (!$this->sn )
        {
            data::$get->sn = 'لا يوجد';
        }
        $this->msn = data::$get->msn;
        $this->notes = data::$get->notes;
        $enter_date = new DateTime();
        $date_input = data::$get->enter_date;
        $date_date = trim(explode(' ',$date_input)[0]);
        $date_time = trim(explode(' ',$date_input)[2]);
        $date_am = trim(explode(' ',$date_input)[3]);
        $year = trim(explode('-',$date_date)[2]);
        $month = trim(explode('-',$date_date)[1]);
        $day = trim(explode('-',$date_date)[0]);
        $hour = trim(explode(':',$date_time)[0]);
        $minute = trim(explode(':',$date_time)[1]);
        if ($date_am == 'PM') $hour += 12;
        if ($hour > 23) $hour -= 12;
        $enter_date->setDate($year,$month,$day);
        $enter_date->setTime($hour,$minute);
        $this->enter_date = $enter_date->format(config::$get->storedatetime);
//print $enter_date->format(config::$get->nlongdate.' '.config::$get->nlongtime24);
        $this->type = data::$get->device_type;
        if (!$this->type)
        {
            $this->error .= "\n يجب كتابة صنف الجهاز ";
        }
        $this->description = data::$get->description;
        if (!$this->description)
        {
            $this->error .= "\n يجب كتابة توصيف العطل";
        }
        $this->adds = data::$get->adds;
        if (!$this->adds) $adds = 'لا يوجد';
        $this->added_by = member::get_id();
        if (!$this->error)
        {
            $this->id = db::$db->insert('maintenance',$this);
            if (!$this->id)
            {
                $this->error .= "\n".db::$db->get_last_error();
            }
        }

        return $this;
    }
    public function saveMSN()
    {
        $result = db::$db->update('maintenance',['msn'=>$this->msn],'id','=',$this->id);
        if (!$result)
        {
            $this->error .= "\n".db::$db->get_last_error();
        }
        return $result;
    }
    public function editFromInput() : maintenance
    {
        $this->error = '';
        $this->customer  = db::$db->select('*','customers','id','=',data::$get->customer)->first()['id'];
        if (!$this->customer)
        {
            $this->error = 'يجب اختيار زبون';
        }
        $this->device = data::$get->device;
        if (!$this->device)
        {
            $this->error .= "\n يجب كتابة اسم الجهاز";
        }
        $this->sn = data::$get->sn;
        if (!$this->sn )
        {
            data::$get->sn = 'لا يوجد';
        }
        $this->msn = data::$get->msn;
        $this->notes = data::$get->notes;
        $enter_date = new DateTime();
        $date_input = data::$get->enter_date;
        $date_date = trim(explode(' ',$date_input)[0]);
        $date_time = trim(explode(' ',$date_input)[2]);
        $date_am = trim(explode(' ',$date_input)[3]);
        $year = trim(explode('-',$date_date)[2]);
        $month = trim(explode('-',$date_date)[1]);
        $day = trim(explode('-',$date_date)[0]);
        $hour = trim(explode(':',$date_time)[0]);
        $minute = trim(explode(':',$date_time)[1]);
        if ($date_am == 'PM') $hour += 12;
        if ($hour > 23) $hour -= 12;
        $enter_date->setDate($year,$month,$day);
        $enter_date->setTime($hour,$minute);
        $this->enter_date = $enter_date->format(config::$get->storedatetime);
//print $enter_date->format(config::$get->nlongdate.' '.config::$get->nlongtime24);
        $this->type = data::$get->device_type;
        if (!$this->type)
        {
            $this->error .= "\n يجب كتابة صنف الجهاز ";
        }
        $this->description = data::$get->description;
        if (!$this->description)
        {
            $this->error .= "\n يجب كتابة توصيف العطل";
        }
        $this->adds = data::$get->adds;
        if (!$this->adds) $adds = 'لا يوجد';
        $this->added_by = member::get_id();
        if (!$this->error)
        {
            $result = db::$db->update('maintenance',$this,'id','=',data::$get->id);
            if (!$result)
            {
                $this->error .= "\n".db::$db->get_last_error();
            }
        }
        return $this;
    }
    public function hasError() : bool
    {
        return !!$this->error;
    }
    public function getError() : String
    {
        return $this->error;
    }
    public function getId()
    {
        return $this->id;
    }
    public function unexit()
    {
        $this->out_date = '';

        $up['stat'] = $this->log->getLastReportStat();
        $up['out_date'] = '';
        $up['out_by'] = null;
        db::$db->update('maintenance',$up,'id','=',$this->id);

    }
    public function getFillInfo() : result
    {
        $result = new result();
        if ($this->hasError()) return $result;
        $result->join($this);
        $result->id = $this->id;
        $c = db::$db->select('*','customers','id','=',$this->customer)->first();
        //if (!$c) dd($this);
        $result->customer_name = $c['name'];
        $result->customer_phone = $c['phone'];
        $result->added_by_name = member::load_member($this->added_by)->name;
        $result->out_by_name = '';
        ($result->sn && $result->msn && $result->snsep = '/') || $result->snsep = '';
        if ($this->stat == 3) {
            $result->btnsToRemove[] = 'deliver_to_customer_btn';
            $result->btnsToRemove[] = 'edit_btn';
            if ($this->out_date) {
                try {
                    $out = new DateTime($this->out_date);
                    $diff = time() - $out->format("U");
                    if ($diff < -10 || $diff > 864000 ) $result->btnsToRemove[] = 'unexit_btn';
                } catch (Throwable $any)
                {
                    $result->btnsToRemove[] = 'unexit_btn';
                }
            }
        } else
        {
            $result->btnsToRemove[] = 're_enter_btn';
            $result->btnsToRemove[] = 'unexit_btn';
        }

        // Case 6643: RONGTA RP80-USE  علي زيود -
        $result->mail_subject = 'Case '.$this->id.': '.$this->device.' '.$result->customer_name.' - '.$result->customer_phone;
        // RONGTA RP80-USE SN :  A8000800720407/F170520
        $result->mail_body = $this->device.' SN : '.$this->sn.'/'.$this->msn.str_replace("\n",' ',$this->log->getLastReport()->report.'');

        /*$result->mail_subject = 'Case : '.$this->id.' '.$result->customer_name.' '.$this->device;
        $result->mail_body = 'Case : '.$this->id.'%0D%0A'.''.$result->customer_name.' / '.$result->customer_phone.'%0D%0A'
        .' '.$this->device.'%0D%0A'.'SN: '.$this->sn.' / '.$this->msn.'%0D%0A'.'الحالة : '.phrases::$stat_name[$this->stat].'%0D%0A'
            .'تقرير الصيانة : ' .'%0D%0A'.$this->log->getLastReport()->sentBy.'%0D%0A'.str_replace("\n",'%0D%0A',$this->log->getLastReport()->report.'')
        .'%0D%0A'.'ملاحظات : '.'%0D%0A'.str_replace("\n",'%0D%0A',$this->notes);*/
        $last_report = $this->log->getLastReport();
        $result->last_log_text = $last_report->report;
        $result->last_log_name = $last_report->sentBy;
        $result->last_log_date = $last_report->log_enter_date;
        foreach ($result as $key => $item) {
            if (is_string($item) ) $result->$key = str_replace("\n",'<br>',$item);
        }
        // after \n to <br> replacement
        if ($this->stat == 3) {
            $result->out_by_name = member::load_member($this->out_by)->name;
            $html = (new style('maintenance_view'))->get_part(2)->fill($result).'';
            $result->delivered_html = $html;
        }
        $result->stat_html = '<span class="float-left badge badge-pill badge-'.phrases::$badge_color[$result->stat].'" >'.phrases::$stat_name[$result->stat].'</span>';
        return  $result;
    }
    public function generateEditVars() : void
    {
        foreach ($this as $key => $value)
        {
            if (!isset(data::$get->$key)) data::$get->$key = $value;
        }
    }
    public function canAddReport() : bool
    {
        return $this->stat != 3; // not delivered
    }
    public function getAllReports() : result
    {
        if (is_null($this->log)) return new result();
        return $this->log->getReports();
    }
    public function getPrevBills() : result
    {
        //$result = new result();
        $sql = 'WHERE (';
        if ($this->sn != '' && $this->sn != 'لا يوجد' && $this->sn != 'لايوجد') $sql .= " sn = '".$this->sn."' ";
        if ($this->msn != '' && $this->msn != 'لا يوجد' && $this->msn != 'لايوجد')
        {
            if ($sql != 'WHERE (')
            {
                $sql .= " OR";
            }
            $sql .= " msn = '".$this->msn."' ";
        }
        if ($sql == 'WHERE (') return new result();
        $sql .= " ) AND id <> '".$this->id."'";
        //dd($sql);
        $tmp =  db::$db->adv_select('*','maintenance',$sql);
        $result = new result();
        foreach ($tmp as $item) {
            foreach ($item as $key => $var) {
                $new_key = 'prev_'.$key;
                //if (is_string($var)) $var = str_replace("\n",'<br>',$var);
                $new_item[$new_key] = $var;
            }
            $c = db::$db->select('*','customers','id','=',$this->customer)->first();
            $new_item['prev_customer'] = $c['name'];
            $lreport = db::$db->select('*','m_log','m_id','=',$new_item['prev_id'],'id','DESC','1')->first();

            $new_item['prev_report'] = str_replace("\n",'<br>',$lreport['report'].'');
            $result[] = $new_item;
        }
        return $result;
    }
}