<?php


class job
{
    static $header,$top,$bottom,$footer,$body;
    public $job;
    function __construct()
    {
        self::$header = new style('header');
        self::$top  = new style('top');
        self::$bottom = new style('bottom');
        self::$footer = new style('footer');
        $this->job = data::$get->job;
        if (config::$get->force_login && member::$current->id < 1) {
            if ($this->job == 'ajax') {
                // show error msg
                create_error('Please Login');
            } else {
                // display login page
                require './jobs/j_login.php';
                // @TODO LOGIN FROM ANY PAGE
                if (member::$current->id > 0 && $this->job != 'login') {
                    $this->execute_job();
                }
            }
        } else {
            $this->execute_job();
        }
    }
    static function clear_html() {
        self::$header = self::$top = self::$bottom = self::$footer = '';
    }
    static function out() {
        print job::$header.job::$top.job::$body.job::$bottom.job::$footer;
    }
    private function execute_job() {

        $file = './jobs/j_'.$this->job.'.php';
//        self::$header = new style('header');
//        self::$footer = new style('footer');
//        self::$top = new style('top');
//        self::$bottom = new style('bottom');
        if (file_exists($file)) {
            require $file;
        } else {
            $this->no_method();
        }
    }
    private function no_method() {
        /**
         * For QR MENU this will show restaurant menu
        */
        data::$get->page_title = '{{error}}';
        $file = './jobs/j_'.$this->job.'.php';
        print style::msgbox('{{file_not_found}}','danger',true);
        die();
    }
}