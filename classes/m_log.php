<?php


class m_log
{
    private ?int $id;
    private result $log;
    private result $cashed_last_report;
    function __construct($id = null)
    {
        $this->id = $id;
        $this->log = new result();
        $this->cashed_last_report = new result();
        if (!$id) return;
        $this->log = db::$db->select('*','m_log','m_id','=',$id,'id','DESC');
    }
    function getLastReportStat()  {
        if ( $this->log->count() > 0)
        {
            return $this->log->first()['new_stat'];
        } else {
            return 0;
        }
    }
    function getLastReport() : result
    {
        if ($this->cashed_last_report->count() > 0) return $this->cashed_last_report;
        $result = new result();
        if ( $this->log->count() > 0)
        {
            $result->log_enter_date = '';
            $result->join($this->log->first());
            $result->sentBy = member::load_member($result->user_id)->name;
            //$result->report = $result->description.'';
            //$result->enter_date = '';
        } else {
            $result->sentBy = '';
            $result->report = 'لا يوجد';
            $result->log_enter_date = '';
        }

        return $result;
    }
    public function getReports() : result
    {
        $result = new result();
        if (!$this->id) return $result;
        foreach ($this->log as $key => $item) {
            $this->{'log'}->{$key}['sentby'] = member::load_member($this->{'log'}[$key]['user_id'])->name;
            $this->{'log'}->{$key}['new_stat_html'] = '<span class="badge badge-pill badge-'.phrases::$badge_color[$item['new_stat']].'" >'.phrases::$stat_name[$item['new_stat']].'</span>';
            $this->{'log'}->{$key}['prev_stat_html'] = '<span class="badge badge-pill badge-'.phrases::$badge_color[$item['prev_stat']].'" >'.phrases::$stat_name[$item['prev_stat']].'</span>';
            //$result->{$key} = $this->{'log'}[$key];
            $this->{'log'}->{$key}['report'] = str_replace("\n",'<br>',$item['report']);
        }
        $result->join($this->{'log'});
        return $result;
    }

}