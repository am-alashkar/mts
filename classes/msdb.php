<?php


class msdb extends mysqli
{
    static $db;
    function __construct()
    {
        die('Legacy not supported');
        $db_database = $db_user = $db_host = $db_pass = $db_pfx = $db_sfx = '';
        require './config/config.php';
        parent::__construct($db_host, $db_user, $db_pass, $db_database, null, null);
        if ($this->connect_errno) {
            create_error('MSSDB ERROR',$this->connect_errno);
        }
        self::$db = $this;
    }
    public function select($select, $table, $where = null, $order = null, $limit = null) {
        $upfx = $this->get_pfx_for($table);
        if (!$col) {
            $sql = 'SELECT '.$select.' FROM `'.$upfx.$table.'`';
        } else {
            if (!$value) {
                $condition = 'IS';
                $value = 'NULL';
            } else {
                $value = "'".$value."'";
            }
            $sql = 'SELECT '.$select.' FROM `'.$upfx.$table.'` WHERE '.$col.' '.$condition."  ".$value." ";
        }
        if ($order_col) {
            $sql .= ' ORDER BY '.$order_col.' '.($order_type ? $order_type : 'ASC');
        }
        if (is_numeric($limit)) {
            $sql .= ' LIMIT '.$limit;
        }
        $error['code'] = 'SQL003';
        $result = $this->dbcon->query($sql) ;
        $this->last_error = $sql.' :: '.$this->dbcon->error;
        $dat = new result();
        if ($result) {
            while ($dta = mysqli_fetch_assoc($result)) {
                $data[] = $dta;
            }
            if (is_array($data)) {
                $dat->join($data);
            }
            mysqli_free_result($result);
        }
        return $dat;
    }
}