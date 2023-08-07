<?php
/**
 * @TODO : $v = ['a'=>'aa','b']; use it for select , or table names ( SELECT a as aa , b ) .. tables: ... FROM a aa .. OR ... FROM b ...
 * @TODO : ->join($table (same as $v), $on , $con , $value) .. also $on , $con , $value can be an array
 * @TODO : ->left_join ->right_join ->outer_join .. etc .
 * @TODO : sql builder .. ->select()->from()->where()->limit()->order_by()->join()->left_join()->right_join()->execute(); // order must not be important
 * @TODO : sql builder .. ->update()->set()->where()->execute(); // order must not be important
 * @TODO : sql builder .. ->delete()->from()->where()->execute();// order must not be important
 * @TODO : sql builder .. ->insert_into()->values()->execute();// order must not be important
*/
class db {

    private $dbcon,$last_error;
    public $pfx,$sfx;
    static $db;
    function __construct()
    {
        $db_database = $db_user = $db_host = $db_pass = $db_pfx = $db_sfx = '';
        require './config/config.php';
        $this->dbcon = new mysqli($db_host,$db_user,$db_pass,$db_database);
        $this->pfx = $db_pfx;
        $this->sfx = $db_sfx;
        $error['details'] = "SQL Connection Error.";
        $error['files'] = 'Core';
        if ($this->dbcon->connect_errno) {
            $error['code'] = 'SQL'.$this->dbcon->connect_errno;
            // $error['details'] .= ' '.$this->dbcon->connect_error;
            require('error.php');
        }
        self::$db = $this;
    }
    public function get_last_error() {
        return $this->last_error;
    }
    private function get_pfx_for($table) {
        switch ($table) {
            case 'members':
            case 'global':
                return $this->sfx;
        }
        return $this->pfx;
    }
    function __destruct()
    {
        $this->dbcon->close();
    }
    // select * from table where id = 1 order by id ASC limit 20;
    // select('*','members','id','=',data::vars->id);
    public function select($select,$from,$col = null,$condition = null,$value = null,$order_col = null
        , $order_type = null,$limit = null) {
        $upfx = $this->get_pfx_for($from);
        if (!$col) {
            $sql = 'SELECT '.$select.' FROM `'.$upfx.$from.'`';
        } else {
            if ($value === null) {
                $condition = 'IS';
                $value = 'NULL';
            } else {
                if ($condition != 'IN') $value = "'".$value."'";
            }
            $sql = 'SELECT '.$select.' FROM `'.$upfx.$from.'` WHERE '.$col.' '.$condition."  ".$value." ";
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
    function adv_select($sel,$table,$sql) {
        $upfx = $this->get_pfx_for($table);
        if ($table) {
            $query = "SELECT " . $sel . " FROM `" . $upfx . $table . "` " . $sql . " ";
        } else {
            $query = $sql;
        }
        $result = $this->dbcon->query($query) ;

        $dat = new result();
        if (is_object($result)) {
            while ($dta = mysqli_fetch_assoc($result)) {
                $data[] = $dta;
            }
            if (is_array($data)) {
                $dat->join($data);
            }
            mysqli_free_result($result);
        }
        $this->last_error =  $query.'::'.  $this->dbcon->error;
        return $dat;
    }
    function update($table,$values,$col = null ,$con = null ,$val = null, $adv = null) : bool
    {
        if (!$col) {
            $this->last_error = 'Update Called with Empty Column name!';
            return false;
        }
        $upfx = $this->get_pfx_for($table);
        if (is_null($val)) {
            $con = 'IS';
            $val = 'NULL';
        } else {
            $val = "'" . $val . "'";
        }
        if (is_array($values) || is_object($values)) {
            $query = "UPDATE `" . $upfx . $table . "` SET ";
            $b = false;
            foreach ($values as $key => $var) {
                if (! is_numeric($key) && $key) {
                    if ($b) {
                        $query .= ' , ';
                    } else {
                        $b = true;
                    }
                    if (!is_null($var)) {
                        if (is_array($var)) {
                            if ($var[0] == 'op') {
                                $query .= "`" . $key . "` = " . $var[1] . " ";
                            } else {
                                $query .= "`" . $key . "` = '" . $var[1] . "' ";
                            }
                        } else {
                            $query .= "`" . $key . "` = '" . $var . "' ";
                        }

                    } else {
                        $query .= "`" . $key . "` = NULL ";
                    }
                }
            }
            if (!$adv) {
                $query .= " WHERE ".$col.' '.$con." ".$val." ;";
            } else {
                $query .= $adv." ;";
            }
            $result = $this->dbcon->query($query) ;
            $this->last_error = $query.'::'.$this->dbcon->error;
            return !!$result;
        }
        $this->last_error = 'Values is null or not an array !';
        return false;
    }
    function insert($table,$values) {
        $upfx = $this->get_pfx_for($table);
        $query1 = $query2 = '';
        if (is_array($values) || is_object($values)) {
            foreach ($values as $key => $var) {
                if (!is_numeric($key)) {
                    $query1 = $query1 . " " . $key . ", ";
                    if (strstr((string) $var, '\'') || strstr((string) $var, '\\')) {
                        global $error;
                        $error['details'] = 'NAC_' . $error['details'].' ON table '.$table;
                        $error['code'] = '403';
                        require('error.php');
                    }
                    if ($var === null) {
                        $query2 = $query2 . " NULL , ";
                    } else {
                        $query2 = $query2 . " '" . $var . "', ";
                    }
                }
            }
        } else {
            $this->last_error = 'No data sent';
            return false;
        }
        $query1 = substr($query1, 0, strlen($query1) - 2);
        $query2 = substr($query2, 0, strlen($query2) - 2);
        $query = "INSERT INTO " . $upfx . $table . " (" . $query1 . " ) VALUES (" . $query2 . " );";
        $this->dbcon->query($query) ;
        $this->last_error = $query.'::'.  $this->dbcon->error;
        if (!$this->dbcon->insert_id && !$this->dbcon->error) return -1;// some tables has no id field
        if ($this->dbcon->error) return 0;
        return $this->dbcon->insert_id;
    }
    function delete($table,$col ,$con = null,$val = null ,$advanced_where = '') {
        $upfx = $this->get_pfx_for($table);
        if (!$col && !$advanced_where) {
            $this->last_error = 'Delete Error : No Column name !?';
            return false;
        }
        if (is_null($val)) {
            $con = 'IS';
            $val = 'NULL';
        } else {
            $val = "'".$val."'";
        }
        if ($advanced_where) {
            $query = "DELETE FROM `" . $upfx . $table . "` " . $advanced_where;
        } else {
            $query = "DELETE FROM `" . $upfx . $table . "` WHERE " . $col . " ".$con." ".$val." ;";
        }

        $result = $this->dbcon->query($query) ;
        $this->last_error = $query.'::'.  $this->dbcon->error;
        return $result ;
    }
    function truncate($table) {
        $upfx = $this->get_pfx_for($table);
        // @TODO : ONLY SUPER ADMIN IS ALLOWED TO RUN THIS
        $query = "TRUNCATE TABLE `" . $upfx . $table . "` ;";
        $result = $this->dbcon->query($query) ;
        $this->last_error = $query.'::'.  $this->dbcon->error;
        return $result ;
    }
}