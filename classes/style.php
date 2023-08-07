<?php


class style
{
    private $html;
    static $vars;
    function __construct($style = null) {
        if (!self::$vars) {
            require_once _STYLE_.'/variables.php';
            /** @var array $var_array */
            self::$vars = $var_array;
        }
        if ($style) {
            $this->html = file_get_contents(_STYLE_.'/'.$style.'.stl');
        } else {
            $this->html = '';
        }
    }
    // $txt = str_replace('{{something}}' , 'anything' , $txt )
    public function str_replace($find , $replace = '',$return_result = null) {
        if (!$return_result) {
            $this->html = str_replace($find,$replace,$this->html.'');
            return $this;
        }
        return str_replace($find,$replace,$this->html);
    }
    public function part($id = 0) {
        if ($id < 1) return $this;
        $this->html = explode('<!-- SEP -->',$this->html)[$id-1];
        return $this;
    }

    /** Same as explode function but if you directly explode the object it will finish() the html before.<br>
     * this method will not finish() it.
     * @param string $delimiter
     * @return false|string[]
     */
    public function split($delimiter) {
        return explode($delimiter,$this->html);

    }
    public function get_part($id = 0) {
        $result = new style();
        if ($id < 1) return $result;
        $result->add_html($this->html);
        $result->add_html(explode('<!-- SEP -->',$this->html)[$id-1],0,1);
        return $result;
    }
    public function add_html($html = '',$before = false , $replace = false) {
        if ($replace) $this->html = $html;
        elseif ($before) $this->html = $html.$this->html;
        else $this->html .= $html;
        return $this;
    }
    public function __toString() {
        $this->finish();
        return $this->html.'';
    }
    public function clear() {
        $this->html = '';
        return $this;
    }
    /**
     * @param string $val
     * @param bool $ignore : (ignores hours and minutes) if true and $auto is false then write different up to day only if the different is more than 1 day
     * @param bool $auto : if true then convert to datetime format if more than 1 day different
     * @return string
     */
    public function tell_me_since_when($val,$ignore = false, $auto = true) {
        try {
            $stop = 0;
            if (is_numeric($val)) $val = date(config::$get->storedate,$val);
            // if (!is_numeric($val)) return;
            $time = new DateTime($val);
            $today = new DateTime(); // now
            $diff = $time->diff($today);
            //$diff = $today->diff($time);
            $array['year'] = $diff->y;
            $array['month'] = $diff->m;
            $array['day'] = $diff->d;
            $array['hour'] = $diff->h;
            $array['minute'] = $diff->i;
            $array['secound'] = $diff->s;
            //var_dump($array);
            $txt = ' ';
            if ($array['year'] > 0) {
                $txt .= ' '.lang::engine_call('tafqit',$array['year'],'year');
                $stop = 1;
            }
            if ($array['month'] > 0) {
                if ($txt != ' ') {
                    $txt = $txt . ' ,';
                }
                $txt .= ' '.lang::engine_call('tafqit',$array['month'],'month');
                $stop = 1;
            }
            if ($array['day'] > 0) {
                if ($txt != ' ') {
                    $txt = $txt . ' ,';
                }
                $txt .= ' '.lang::engine_call('tafqit',$array['day'],'day');
                $stop = 1;
            }
            if ($auto && $stop) {
                $str = config::$get->nshortdate.' @ '.config::$get->nshorttime12;
                //$txt = date($str,$time).'s';
                $txt = $time->format($str);
                return $txt;
            }
            if ($ignore && $stop) {
                return $txt;
            }
            if ($array['hour'] > 0) {
                if ($txt != ' ') {
                    $txt = $txt . ' ,';
                }
                $txt .= ' '.lang::engine_call('tafqit',$array['hour'],'hour');
                $stop = 1;
            }
            if ($array['minute'] > 0) {
                if ($txt != ' ') {
                    $txt = $txt . ' ,';
                }
                $txt .= ' '.lang::engine_call('tafqit',$array['minute'],'minute');
//              $stop = 1;
            }
            $txt = ' '.lang::engine_call('sincer',$diff->invert,$txt);
            return $txt;
        } catch (Exception $exception) {
            return '';
        }
    }
    public function got_time($val, $format) {
        try {
            $time = new datetime($val.'');
            if (config::$get->$format) {
                $txt = $time->format(config::$get->$format);
            } else {
                $txt = $time->format($format);
            }
        } catch (Exception $e) {
            $txt = '';
        }
        return $txt;
    }
    private function finish() {
        $txt = $this->html;
        if (member::$current->id) {
            $str2f = "/<!-- GUEST_START -->(.*?)<!-- GUEST_END -->/s";
            while (preg_match($str2f, $txt.'')) {
                $txt = preg_replace($str2f, "", $txt); // hide them !
            }
        } else {
            $str2f = "/<!-- MEMBERS_START -->(.*?)<!-- MEMBERS_END -->/s";
            while (preg_match($str2f, $txt.'')) {
                $txt = preg_replace($str2f, "", $txt); // hide them !
            }
        }
        $str2f = '/<!-- PER_(.*?) -->(.*?)<!-- \/PER_(.*?) -->/s';
        if (preg_match_all($str2f, $txt.'', $array)) {
            //debug::add('TEST');
            $vals = array_unique($array['1']);
            foreach ($vals as $item=>$value){
                $find = '<!-- PER_'.$value.' -->'.$array[2][0].'<!-- /PER_'.$array[3][0].' -->';
                //debug::add($find);
                if (!allowed($value)) {
                    $txt = str_replace($find,'',$txt);
                } else {
                    $txt = str_replace($find,(string) $array[2][0],$txt);
                }

            }
        }
        // <!-- OBJECT_add_btn -->
        for ($i=1;$i<3 ; $i++)
            if (preg_match_all('/<!-- OBJECT_(.*?) -->/s', $txt.'', $array)) {
                $array = array_unique($array['1']);
                foreach ($array as $item=>$value){
                    $find = '<!-- OBJECT_'.$value.' -->';
                    $funname = 'obj_'.$value; // obj_add_btn
                    if (function_exists($funname)) {
                        $replace = $funname();
                    } else {
                        $replace = '';
                    }
                    $txt = str_replace($find,(string) $replace,$txt);
                }
            }
        if (preg_match_all('/<!-- VAR_(.*?) -->/s', $txt.'', $array)) {
            $array = array_unique($array['1']);
            foreach ($array as $item=>$value){
                $find = '<!-- VAR_'.$value.' -->';
                if (strstr($value,'|')) {
                    $tmp = explode('|',$value);
                    $value = $tmp['0'];
                    try {
                        $test = new DateTime(data::$get->$value.'');
                        if ($tmp['1'] == 'since') {
                            $replace = $this->tell_me_since_when(data::$get->$value);
                        } elseif ($tmp['1'] == 'auto') {
                            $replace = $this->tell_me_since_when(data::$get->$value,'0','1');
                        } else {
                            $replace = $this->got_time(data::$get->$value,$tmp['1']);
                        }
                    } catch (exception $exception){
                        $replace = '';
                    }

                } elseif (strstr($value,'>')) {
                    $tmp = explode('>',$value);
                    $replace = data::$get->{$tmp[0]}[$tmp[1]];
                } elseif (isset(data::$get->$value)) {
                    $replace = data::$get->$value;
                } else {
                    $replace = '';
                }
                $txt = str_replace($find,(string) $replace,$txt);
            }
        }

        if (preg_match_all('/<!-- CONFIG_(.*?) -->/s', $txt.'', $array)) {
            $array = array_unique($array['1']);
            foreach ($array as $item=>$value){
                $find = '<!-- CONFIG_'.$value.' -->';
                if (isset(config::$get->$value)) {
                    $replace = config::$get->$value;
                } else {
                    $replace = '';
                }
                $txt = str_replace($find,(string) $replace,$txt);
            }
        }
        if (preg_match_all('/<!-- MEMBER_(.*?) -->/s', $txt.'', $array)) {
            $array = array_unique($array['1']);
            foreach ($array as $item=>$value){
                $find = '<!-- MEMBER_'.$value.' -->';
                if (isset(member::$current->$value) && !strstr($value,'pass')) {
                    $replace = member::$current->$value;
                } else {
                    $replace = '';

                }
                $txt = str_replace($find,(string) $replace,$txt);
            }
        }
        $txt = lang::localize($txt,true);
        $this->html = $txt;
    }
    static function scroll($top = true) {
        if ($top) {
            $ht = '<script>
$("html, body").animate({scrollTop: 0}, "slow");
</script>';
        } else {
            $ht = '<script>
$("html, body").animate({scrollTop: document.body.scrollHeight}, "slow");
</script>';
        }
        return $ht;
    }
    public function add($style,$before = false) {
        $html = file_get_contents(_STYLE_.'/'.$style.'.stl');
        if ($before) $this->html = $html . $this->html;
        else $this->html .= $html;
        return $this;
    }

    /**
     * @param string $msg Text Message
     * @param string $type success , danger , warning , info
     * @param bool $fullscreen removes top , bottom ,
     * @return style
     */
    static function msgbox($msg, $type = 'success', $fullscreen = false) {
        $main = new style('msgbox');
        if ($fullscreen) $html = $main->get_part(1);
        else $html = $main->get_part(2);
        $html->str_replace('<!-- CONTENTS -->',(string) $msg);
        $html->str_replace('<!-- TYPE -->',(string) $type);
        if ($fullscreen){
            job::clear_html();
            $html->add('header',true)->add('footer');
        }
        return $html;
    }
    static function gritter($style = 'center',
                            $icon = 'success',
                            $msg = '',
                            $title = '',
                            $time=0,
                            $confirm = false,
                            $is_toast = true,
                            $remove_modal = true) {
        if (!$style) $style = 'center';
        if (!$icon) $icon = 'success';
        $msg = lang::localize($msg,true);
        $msg = str_replace("\n",(string) '',(string) $msg);
        $title = lang::localize($title,true);
        $title = str_replace("\n",'',(string) $title);
        $btn = lang::localize('{{ok}}',false,true);
        $gritter = file_get_contents('./'._STYLE_.'/gritter.stl');
        $gritter = explode('<!-- SEP -->',$gritter);
        $html = $gritter['0'];
        $html = str_replace('<!-- ICON -->',$icon,$html);
        $html = str_replace('<!-- BTN -->',$btn,$html);
        $html = str_replace('<!-- MSG -->',$msg,$html);
        $html = str_replace('<!-- STYLE -->',$style,$html);
        $con = $confirm ? 'true' : 'false';
        $html = str_replace('<!-- CONFIRM -->',$con,$html);
        $time = $time > 0 ? ',
        timer: '.$time : '';
        $html = str_replace('<!-- TIME -->',$time,$html);
        $toast = $is_toast ? 'true' : 'false';
        $html = str_replace('<!-- TOAST -->',$toast,$html);
        $html = str_replace('<!-- TITLE -->',$title,$html);
        if ($remove_modal) {
            $html .= $gritter['1'];
        }
        $html .= $gritter['2'];
        return $html;

    }
    static function big_error_gritter($msg,$title = ''){
        if (!$title) {
            $title = $msg;
            $msg = '';
        }
        return self::gritter('center','error',$msg,$title,0,true,false,true);
    }
    static function error_gritter($msg,$remove_modal = true) {
        return self::gritter('center','error',$msg,'',5000,false,true,$remove_modal);
    }
    static function big_success_gritter($msg,$title = '') {
        if (!$title) {
            $title = $msg;
            $msg = '';
        }
        return self::gritter('center','success',$msg,$title,0,true,false,true);
    }
    static function success_gritter($msg='',$remove_modal = true) {
        return self::gritter('center','success',$msg,'',5000,false,true,$remove_modal);
    }
    static function set_input($input_id,$stat) {
        return '<script>
$("#'.$input_id.'").addClass("'.$stat.'");
</script>';
    }
    /**
     * @param string $table_id Table id
     * @param null|result|array $data
     * @param bool $keep_if_empty if true will remove repeated section only if $data is null
     *                              if false will remove the whole table if $data is null
     * @param bool $sub_item
     * @param string $empty_replace what to put if no data and $keep_if_empty is false
     * @return $this
     */
    function fill_table($table_id , $data = null, $keep_if_empty = false, $sub_item = false,$empty_replace = '') {

        if (preg_match_all('/(.*)\<table(.*?)id\=\"'.$table_id.'\"(.*?)\<thead\>(.*?)\<\/thead\>(.*?)\<tbody\>(.*?)\<\/tbody\>(.*?)\<\/table\>(.*)/s', $this->html, $array)) {
//            var_dump($array);
//            die();
            $header = '<table'.$array['2']['0'].'id="'.$table_id.'"'.$array['3']['0'].'<thead>'.$array['4']['0'].'</thead>'.$array['5']['0'].'<tbody>';
            $repeat = $array['6']['0'];
            $footer = '</tbody>'.$array['7']['0'].'</table>';
            $old = $header.$repeat.$footer;
            //var_dump($old);
            //die();
            //$old = $array['0']['0'];
        } else {
            //$this->html = '';
            return $this;
        }
        if (is_array($data) || is_object($data)) {
            if (is_object($data))
                if (method_exists($data,'count'))
                    if ($data->count() < 1) {
                        $replaced_html = $empty_replace;
                        if ($keep_if_empty) $replaced_html = $header.'<!-- CONTENTS -->'.$footer;
                        $this->str_replace($old,$replaced_html);
                        return $this;
                    }
            $rpt = '';
            $counter = 0;
            foreach ($data as $key=>$value) {
                if ((is_array($value) || (is_object($value) && get_class($value) == 'result') ) && is_numeric($key)) {
                    $counter++;
                    if ($sub_item) {
                        $value[$sub_item]['counter'] = $counter;
                        $rpt .= $this->fill($value[$sub_item],$repeat);
                    } else {
                        $value['counter'] = $counter;
                        $rpt .= $this->fill($value,$repeat);
                    }

                }
            }
            $replaced_html =  $header.$rpt.$footer;//.'TEST';
            $this->str_replace($old,$replaced_html);

        } elseif (!$keep_if_empty) {
            $this->str_replace($old,$empty_replace);
        } else {
            $this->str_replace($old,$header.'<!-- CONTENTS -->'.$footer);
        }
        return $this;
    }

    /**
    * @param string $table_id Table id
    * @return string
    */
    function get_table($table_id) {

        if (preg_match_all('/(.*)\<table(.*?)id\=\"'.$table_id.'\"(.*?)\<thead\>(.*?)\<\/thead\>(.*?)\<tbody\>(.*?)\<\/tbody\>(.*?)\<\/table\>(.*)/s', $this->html, $array)) {
            $header = '<table'.$array['2']['0'].'id="'.$table_id.'"'.$array['3']['0'].'<thead>'.$array['4']['0'].'</thead>'.$array['5']['0'].'<tbody>';
            $repeat = $array['6']['0'];
            $footer = '</tbody>'.$array['7']['0'].'</table>';
            return $header.$repeat.$footer;
        }
        return '';
    }

    /**
     * Replaces &lt;!-- INFO_(??) --&gt; with value of $data[??] or $data->{??} .
     * @param array|result $data Data to look for in $this->html or in $sht
     * @param string|style $sht if set then will not look inside $this->html , instead it will look inside $sht and return $sht after replacing<br>
     *                          if not set will return $this after replacing $this->html
     * @param bool $leave_none if set to true then not found phrases will not be deleted - if false will delete any values not found in $data
     * @return $this|string
     */
    function fill($data = null , $sht = null, $leave_none = false) {
        $sht .= '';
        if (!$sht) {
            $sht = $this->html;
            $return = false;
        } else {
            $return = true;
        }
        if (is_object($data)) {
            foreach ($data as $k=>$v) {
                $new[$k] = $v;
            }
            $data = $new;
        }
        if (is_array($data) ) {
            foreach ($data as $key => $var) {
                $find = '<!-- INFO_' . $key . ' -->';
                if (!isset($data[$key]) && $leave_none) continue;
                $replace = $data[$key];
                $sht = str_replace($find, (string) $replace, $sht.'');
            }
            $str2f = '/<!-- INFO_(.*?)\|(.*?) -->/';
            if (preg_match_all($str2f, $sht.'', $array)) {
                foreach ($array['0'] as $key => $var) {
                    $find = '<!-- INFO_' . $array['1'][$key] . '|' . $array['2'][$key] . ' -->';
                    // got_time($time,$type)
                    $time = $data[$array['1'][$key]];
                    try {
                        if ($time) {
                            $timetest = new DateTime($time);
                            if ($array['2'][$key] == 'since') {
                                // once it is over 1 day it will ignore hours and minutes
                                $replace = $this->tell_me_since_when($time,true,false); // C2 v>0
                            } elseif ($array['2'][$key] == 'auto') {
                                // once it is over 1 day it will convert to datetime
                                $replace = $this->tell_me_since_when($time,false,true); // C2 v>0
                            } elseif ($array['2'][$key] == 'full_since') {
                                // write all difference
                                $replace = $this->tell_me_since_when($time,false,false); // C2 v>0
                            } else {
                                $replace = $this->got_time($time, $array['2'][$key]); // C2 v>0
                            }
                        } else {
                            $replace = '';
                        }

                    } catch (Exception $e) {
                        $replace = '';
                    }
                    if (!$replace && $leave_none) continue;
                    $sht = str_replace($find, $replace, $sht);
                }
            }
            if (preg_match_all('/<!-- INFO_(.*?) -->/', $sht.'', $array)) {
                $array = array_unique($array['1']);
                foreach ($array as $item=>$value){
                    $find = '<!-- INFO_'.$value.' -->';
                    if (strstr($value,'>')) {
                        $tmp = explode('>',$value);
                        $replace = $data[$tmp[0]][$tmp[1]];
                        $sht = str_replace($find,(string) $replace,$sht.'');
                    }

                }
            }
            $str2f = '/<!-- INFO_(.*?) -->/';
            if(!$leave_none) while (preg_match($str2f,$sht.'')) {
                $sht = preg_replace($str2f, "", $sht);
            }
        }
        //   var_dump($data);
        if ($return) {
            return $sht.'';
        } else {
            $this->html = $sht.'';
            return $this;
        }
    }
    function clear_unfilled($with='')
    {
        $str2f = '/<!-- INFO_(.*?) -->/';
        while (preg_match($str2f,$this->html.'')) {
            $this->html = preg_replace($str2f, $with, $this->html);
        }

    }
    function fill_template($data = null, $return_if_empty = false, $sub = false){

        if (preg_match_all('/(.*?)\<\!\-\- HEADER_START \-\-\>(.*?)\<\!\-\- HEADER_END \-\-\>(.*?)\<\!\-\- RPT_START \-\-\>(.*?)\<\!\-\- RPT_END \-\-\>(.*?)\<\!\-\- FOOTER_START \-\-\>(.*?)\<\!\-\- FOOTER_END \-\-\>(.*?)/s', $this->html, $array)) {
            $header = $array['2']['0'];
            $repeat = $array['4']['0'];
            $footer = $array['6']['0'];
        } else {
            $this->html = 'failed to expand template';
            return $this;
        }
        if (is_array($data) || is_object($data)) {
            if (is_object($data))
                if (method_exists($data,'count'))
                    if ($data->count() < 1) {
                        $this->html = '';
                        if ($return_if_empty) $this->html = $header.'<!-- CONTENTS -->'.$footer;
                        return $this;
                    }
            $rpt = '';
            $counter = 0;
            foreach ($data as $key=>$value) {
                if (is_array($value) && is_numeric($key)) {
                    $counter++;
                    if ($sub) {
                        $value[$sub]['counter'] = $counter;
                        $rpt .= $this->fill($value[$sub],$repeat);
                    } else {
                        $value['counter'] = $counter;
                        $rpt .= $this->fill($value,$repeat);
                    }
                }
            }
            $this->html =  $header.$rpt.$footer;
        } elseif (!$return_if_empty) {
            $this->html = '';
        } else {
            $this->html = $header.'<!-- CONTENTS -->'.$footer;
        }
        return $this;
    }
    static function variable($key) {
        if (!self::$vars) new style();
        return self::$vars[$key];
    }
    /**
     * Returns the button html in $this->html
     * @param null|string $btn_id
     * @return style
     */
    public function get_btn($btn_id = null) {
        $html = new style();
        if (!$btn_id) return $html;
        $str2f = '/(.*)\<button(.*?)id\=\"'.$btn_id.'\"(.*?)\<\/button\>(.*)/s';
        if (preg_match($str2f,$this->html,$array)) {
            $found = '<button'.$array[2].'id="'.$btn_id.'"'.$array[3].'</button>';
            $html->add_html($found);
        } else {
            die('Not Found '.$btn_id);
        }
        return $html;
    }
    public function del_btn($btn_id = null) {
        if (!$btn_id) return $this;
        $str2f = '/(.*)\<button(.*?)id\=\"'.$btn_id.'\"(.*?)\<\/button\>(.*)/s';
        if (preg_match($str2f,$this->html,$array)) {
            $found = '<button'.$array[2].'id="'.$btn_id.'"'.$array[3].'</button>';
            $this->str_replace($found,'');
        }
        return $this;
    }
    /**
     * Generates a checkbox with provided data
     * @param string $id checkbox id
     * @param string $text Text for label
     * @param string $form_name form name : form_name=""
     * @param bool $checked true for checked or false
     * @return array ['checkbox'] checkbox html , ['label'] checkbox label html
     */
    static function check_box($id = '', $text='', $form_name = 'default_form', $checked = false)
    {
        $main = new style('check_box');
        if (!$id) $id = 'check_box_'.rand(1,9999);
        if (!$text) $text = $id;
        $info = ['id'=>$id,'name'=>$text,'form_name'=>$form_name,'checked'=>$checked? 'checked' : ''];
        $result['checkbox'] = $main->get_part(1)->fill($info).'';
        $result['label'] = $main->get_part(2)->fill($info);
        return $result;
    }
    /**
     * Surround $this->html with modal html and scripts
     * if there must be buttons add them to $this->html before creating a modal
     * you can use &lt;!-- RID --&gt; inside $this->html for a unique id
     * @param string $title Title of the modal window
     * @return $this
     */
    public function create_modal($title = '') {
        $rid = rand(1,1000).time();
        $modal_ht = file_get_contents('./'._STYLE_.'/modal.stl');
        $this->html = str_replace('<!-- CONTENTS -->',$this->html,$modal_ht);
        $this->html = str_replace('<!-- TITLE -->',$title,$this->html);
        $this->html = str_replace('<!-- RID -->',$rid,$this->html);
        return $this;
    }
}