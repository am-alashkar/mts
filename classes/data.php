<?php


class data
{
    public $now,$job,$all,$lmscript,$page_title,$hook,$timezone,$todo;
    static $get;
    public $is_export,$export_data;

    function __construct()
    {

        foreach ($_POST as $key => $value) {
            $this->$key = $value;

        }
        $this->job = $_GET['job'];
        $this->all = $_GET['all'];
        $this->now = date('Y-m-d H:i:s');
        $this->timezone = date_default_timezone_get();
        self::$get = $this;
    }
    static function encode($data) {
        return base64_encode(json_encode($data,JSON_INVALID_UTF8_IGNORE));
    }
    static function decode($data) : ?array {

        return json_decode(base64_decode((string) $data),true);
    }


}
