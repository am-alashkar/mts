<?php


class facebook_reader
{
    private $url, $response;

    function __construct()
    {
        require_once __DIR__.'/vendor/Facebook/autoload.php';

    }
    public function read_contents() {

    }
    public function get_html() {
        $ans = "";
        $ans = $this->response;
        return $ans;
    }
}