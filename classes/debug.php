<?php


class debug
{
    static $file = 'debug.txt';
    static function add($txt) {
        file_put_contents(self::$file,$txt."\n".date('Y-M-d H:i:s')."\n\n",FILE_APPEND);
    }
    static function start() {
        file_put_contents(self::$file,"Start : ".date('Y-M-d H:i:s')."\n\n",FILE_APPEND);
    }
    static function end() {
        file_put_contents(self::$file,"End : ".date('Y-M-d H:i:s')."\n\n",FILE_APPEND);
    }
}