<?php


class lang_en
{
    private  $lang = array(
        'home' => 'Home',
        'year' => 'Year',
        'years'=> 'Years',

    );
    public function tafqit($number,$unit) {
        if ($number > '1') {
            return $number .' '. lang::$lang->get($unit.'s');
        }
        return $number .' '. lang::$lang->get($unit);

    }
    public function sincer($invert,$txt) {
        if ($txt == ' ') return $invert ? lang::$lang->get('a_few_seconds_ago') :  lang::$lang->get('after_a_few_seconds');
        return $invert ? lang::$lang->get('after').' '.$txt : lang::$lang->get('since').' '.$txt ;
    }
    public function get($key) {
        return $this->lang[strtolower($key)];
    }
    public function is_set($key) {
        //if (!isset($this->{'lang'}[strtolower($key)])) print 'Tested : '.$key.' False<br>';
        return isset($this->lang[strtolower($key)]);
    }
}