<?php

class json_out
{
    public $version_id;
    static $buffer;
    private $type = 'A';
    function __construct()
    {
        $this->version_id = 1;
        self::$buffer = $this;
    }
    function __toString()
    {
        switch ($this->type)
        {
            case 'A': // Android (UBX v9 and later)
                return 'OK<->'.base64_encode(json_encode($this,JSON_INVALID_UTF8_IGNORE)).'<->EOT';
            case 'J': // normal Json
                return json_encode($this,JSON_INVALID_UTF8_IGNORE).'';
            case 'E': // base64 Json
                return base64_encode(json_encode($this,JSON_INVALID_UTF8_IGNORE)).'';
        }
        return '';
    }
    public function join($items) {
        foreach ($items as $key=>$value) {
            $this->$key = $value;
        }
        return $this;
    }
    public function out() {
        print self::$buffer.'';
        die();
    }
    public function set_output_to_normal_json() {
        $this->type = 'J';
        return $this;
    }
    public function set_output_to_base64_json() {
        $this->type = 'E';
        return $this;
    }
    public function set_output_to_ubx_format() {
        $this->type = 'A';
        return $this;
    }
}