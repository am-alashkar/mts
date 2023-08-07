<?php


class lang
{
    /**
     * @var lang_ar $lang_engine;
     */
    private $lang_engine;
    static $lang;
    function __construct($lang = null)
    {

        $file = './languages/lang_'.$lang.'.php';
        if (!file_exists($file)) {
            if (member::$current->lang_name){
                //die(member::$current->lang_name);
                $lang = member::$current->lang_name;
                $file = './languages/lang_' . $lang . '.php';
            }
        }
        if (!file_exists($file)) {
            //die(_LANG_);
            $lang = _LANG_;
            $file = './languages/lang_' . _LANG_ . '.php';
        }
        if (!file_exists($file)) create_error('Load Language Error',502);
        //print 'LANG '.$file.'<hr>';
        require $file;
        $lang_class = 'lang_'.$lang;
        $lang = new $lang_class();
        $this->lang_engine = $lang;
        self::$lang = $this;
    }
    static function localize($txt , $delete_unknown = false,$unknown_show_text = true) {
        if (!self::$lang) new lang();
        return self::$lang->localizer($txt , $delete_unknown,$unknown_show_text);
    }
    public function localizer($txt,$delete_unknown = false,$unknown_show_text = true) {
        // {{hello}} {{user}}
        if (preg_match_all('/\{\{(.*?)\}\}/s', $txt.'', $array)) {
            $array = array_unique($array['1']);
            foreach ($array as $value){
                $find = '{{'.$value.'}}';
                if ($this->lang_engine->is_set($value)) {
                    $txt = str_replace($find,$this->lang_engine->get($value),$txt);
                } else if ($delete_unknown) {
                    $txt = str_replace($find,'',$txt);
                } else if ($unknown_show_text) {
                    $txt = str_replace($find,$value,$txt);
                }
            }
        }
        return $txt;
    }
    static function engine_call($function_name , ...$args) {
        if (!self::$lang) new lang();
        return lang::$lang->call_engine($function_name,...$args);
    }
    public function call_engine($function_name , ...$args) {
        try {
            return $this->lang_engine->$function_name(...$args);
        } catch (Exception $exception) {
            return '';
        }
    }
    public function get($key) {
        return $this->lang_engine->get($key);

    }
}