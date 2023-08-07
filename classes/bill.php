<?php


class bill
{
    public $id,$script,$amount,$price,$info,$history,$company,$phone,$name,$sub_script;
    public $admin_id , $admin_note,$done_date,$stat;
    public $user_id=0,$notes='',$sender_id=0,$send_date,$ba=1,$done=0,$notify=0,$cr='',$exesc='';
    private $ok,$stat_html;
    static $bill_stats = ['جديد' , 'منفذة' , 'ملغاة','معلقة','قيد التنفيذ'];
    function __construct($data = null)
    {
        // empty bill or new bill or load bill
        if (is_array($data)) {
            // new bill with data
            foreach ($data as $key => $value) {
                if (isset($this->$key)) $this->$key = $value;
            }
        } elseif (is_numeric($data))
        {
            // load bill
            $this->load_bill($data);
        } else {
            // new empty
        }
        if (!$this->send_date) $this->send_date = date(config::$get->storedatetime);
    }
    private function load_bill($id)
    {
        // load from db ..
        // bills : id - stat - bill_id
        $tmp = db::$db->select('*','bills','id','=',$id)->first();
        if (!$tmp) return $this;
        foreach ($tmp as $key => $value) {
            $this->$key = $value;
        }
        if (!$this->sender_id) $this->sender_id = $this->user_id;
        $this->decode();
    }
    private function decode()
    {
        if (!is_array($this->info) ) $this->info = data::decode($this->info);
        if (!is_array($this->history) ) $this->history = data::decode($this->history);
        //if (!is_array($this->html) ) $this->html = data::decode($this->html);
    }
    private function encode()
    {
        if (is_array($this->info) ) $this->info = data::encode($this->info);
        if (is_array($this->history) ) $this->history = data::encode($this->history);
        //if (is_array($this->html) ) $this->html = data::encode($this->html);
    }
    public function set_stat($stat,$admin_note=null)
    {
        // set stat and save to db if possible
        $this->decode();
        $history['admin_id'] = $this->admin_id ? $this->admin_id : member::get_id();
        $history['stat'] = $this->stat;
        $history['note'] = $this->admin_note;
        $history['done_date'] = date(config::$get->storedatetime);
        $this->stat = $stat;
        $this->ba = $this->stat == '2' ? null:$this->ba;
        $this->done = ($this->stat == '2' || $this->stat == '1') ? 1 : $this->done;
        $this->done_date = date(config::$get->storedatetime);
        $this->admin_id = member::$current->id;
        $this->admin_note = $admin_note;
        $this->history[] = $history;
        $this->notify = $this->admin_id != $this->user_id;
    }
    public function insert()
    {
        if (!$this->amount) $this->amount = 0;
        //if (!$this->sender_id) $this->sender_id = member::$current->id;
        if (!$this->send_date) $this->send_date = date(config::$get->storedatetime);
        if (!$this->user_id) $this->user_id = member::$current->id;
        if (!$this->price || !is_numeric($this->price)) return 0;
        if (!$this->balance()['ok']) return 0;
        if (!$this->ba) $this->ba = '0';
        //$this->is_ok = $this->stat == '2' ? null:1;
        //$this->notify = 1;
        $this->encode();
        if ($this->id) {
            $r =  db::$db->update('bills',$this,'id','=',$this->id);
            bill::balance_set($this->user_id);
            return $r;
        }
        $this->notify = '0';
        unset($this->id);
        $this->id = db::$db->insert('bills',$this);
        //if (!$this->id) die(db::$db->get_last_error());
//        if ($this->id && $this->ba) {
//            db::$db->update('members',['balance'=> [ 0=>'op',1=>'balance - '.$this->price ] ] ,'id','=',$this->user_id);
//        }
        bill::balance_set($this->user_id);
        return $this->id;
    }
    public function update() {
        if (!$this->amount) $this->amount = 0;
        //if (!$this->sender_id) $this->sender_id = member::$current->id;
        if (!$this->send_date) $this->send_date = date(config::$get->storedatetime);
        if (!$this->user_id) $this->user_id = member::$current->id;
        if (!$this->price || !is_numeric($this->price)) return 0;
        if ($this->stat == '1' && $this->done) {
            if (!$this->balance()['ok'] ) return -1;
        }
        //$this->is_ok = $this->stat == '2' ? null:1; some scripts must not be calculated
        //$this->notify = 1;
        $this->encode();
        if ($this->id) {
            db::$db->update('bills', $this, 'id', '=', $this->id);
            bill::balance_set($this->user_id);
            return 1;
        }
        return 0;
    }
    public function balance() {

        //$user = member::load_member($this->user_id); // this will fail if multi bills sends in the same session cause balance will not be updated !
        $user = db::$db->select('balance , credit','members','id','=',$this->user_id)->first();
        $credit = +$user['credit'];
        if ($this->script == 'payment_card') $credit = 0;
        $bal = +$user['balance'];
        if ($this->id) $old_price = +db::$db->select('price','bills','id','=',$this->id)->first()['price'];
        else $old_price = 0;
        $balance['balance'] = $bal + $old_price - $this->price;
        $balance['ok'] = (is_numeric($this->price)) && ($this->price != 0) && ($balance['balance'] >= 0 - $credit);
        $balance['ok'] = $balance['ok'] || $this->script == 'payment';
        if (!is_numeric($this->price) || ( $this->price < 1  && $this->script != 'payment' ) || $this->price == 0) $balance['ok'] = false;
        //print 'in'.$this->price;
        if ($this->script == 'payment_card' && !$balance['ok'])
            if (allowed('is_accountant')) $balance['ok'] = true;
        $this->ok = !!$balance['ok'];
        return $balance;
    }
    public function is_ok(){
        //$this->balance();
        return !!$this->ok;
    }
    public function to_array() {
        $this->decode();
        foreach ($this as $key => $v) {
            $ar[$key] = $v;
        }
        return $ar;
    }
    public function html_vars() {
        $this->decode();
        $info = $this->info;
        foreach ($this as $key => $va) {
            if (!isset($info[$key]) ) $info[$key] = $va;
        }
        if ($this->sender_id != $this->user_id) {
            $info['sent_by'] = '<br>بواسطة : '.member::load_member($this->sender_id)->name;
        }
        $info['stat_html'] = bill::stat_ht($this);
        $info['btns'] = $this->btns();
        $info['user_name'] = member::load_member($this->user_id)->name;
        $fields = $info['info']['fields'];
        if (is_array($fields)) foreach ($fields as $key=>$field) {
            if ($field['hide_from_staff'])
                if ($this->user_id != member::get_family_id()) {
                    unset($info['info']['fields'][$key]);
                    continue;
                }
            if ($field['copy_btn']) {
                $info['info']['fields'][$key]['copy'] = '<button class="btn btn-outline-dark btn-sm" onclick="copy_to_clip(\''.$field['value'].'\')" ><i class="fa fa-copy"></i> </button>';
            }
        }
        return $info;
    }
    public function btns($only_btns = false) {
        $this->decode();
        // special btn ?
        // in case of robotic process
        $html = new style('bill_btn_html');
        $btns = $html->get_part(1);
        $html->part(2);
        $btn_html = '';
        if ($this->done) {
            $html->clear();
            return $html;
        }
        // not done ..
        // stat = 0 : one btn : take it
        if (!$this->stat || $this->stat == '4') {
            if ($this->user_id == member::$current->id || $this->sender_id == member::$current->id ) {
                if (allowed('can_cancel_' . $this->script) && !$this->cr) $btn_html .= $btns->get_btn('cancel_bill_btn')->fill($this);
            }
        }

        if (!$this->done && allowed('can_do_bills') && allowed($this->script.'_staff')) {
            if ($this->admin_id == member::$current->id) {
                // accept / reject / hold
                $btn_html .= $btns->get_btn('accept_bill_btn')->fill($this);
                if ($this->stat != '3' ) $btn_html .= $btns->get_btn('hold_bill_btn')->fill($this);
                else $btn_html .= $btns->get_btn('un_hold_bill_btn')->fill($this);
                $btn_html .= $btns->get_btn('reject_bill_btn')->fill($this);
            } else if ($this->admin_id) {
                // override take
                $btn_html .= $btns->get_btn('override_take_bill_btn')->fill($this);
            } else {
                $btn_html .= $btns->get_btn('take_bill_btn')->fill($this);
            }
        }

        // stat = 3 : if not same admin : one btn : take over it
        //              if same : 2 btn : accept , reject with admin note
        // stat = 4 : if not same admin : one btn : take over it
        //              if same : 3 btn : accept , reject , hold . with admin note
        if (!$only_btns) {
            $html->str_replace('<!-- BTNS -->',$btn_html.'');
        }  else {
            $html->add_html($btn_html,true,true);
        }
        if (!$btn_html) $html->clear();
        return $html;
    }

    /**
     * @param result $bills
     * @param bool $balance_added add balance calculate field
     * @param int $start_bal balance field start
     * @return result
     */
    static function get_fillables($bills,$balance_added = false,$start_bal = 0) {
        //if ($bills->is_empty()) return $bills;
        foreach ($bills as $key => $item) {
            if (!is_array($item['info'])) $info = data::decode($item['info']);
            foreach ($bills->{$key} as $k => $v) {
                $info[$k] = $bills->{$key}[$k];
            }
            $info['stat_html'] = bill::stat_ht($bills->{$key});
            $info['stat_html_mini'] = bill::stat_ht($bills->{$key},true);
            if ($balance_added) {
                $start_bal -= $item['price'];
                $info['balance'] = $start_bal;
            }
            $info['user_name'] = member::load_member($item['user_id'])->name;
            if ($item['user_id'] != $item['sender_id'])
                $info['sent_by'] =  '{{sent_by}} : ' . member::load_member($item['sender_id'])->name;

            $bills->{$key}['info'] = $info;

        }
        return $bills;
    }

    /**
     * @param bill $bill
     * @param bool $mini
     * @return string|style
     */
    static function stat_ht($bill,$mini=false) {
        if ($mini) $html = new style('bill_stats_html_mini');
        else $html = new style('bill_stats_html');
        if (!$bill) create_error('invalid bill');
        if (is_array($bill)) {
            $stat = $bill['stat'];
            $admin_id = $bill['admin_id'];
            $admin_note = $bill['admin_note'];
            $done_date = $bill['done_date'];
            $cr = $bill['cr'];
            $done = $bill['done'];
        } else {
            $stat = $bill->stat;
            $admin_id = $bill->admin_id;
            $admin_note = $bill->admin_note;
            $done_date = $bill->done_date;
            $cr = $bill->cr;
            $done = $bill->done;
        }
        if (data::$get->is_export) {
            return bill::$bill_stats[+$stat];
        }
        if ($stat) {
            $info['admin_name'] = member::load_member($admin_id)->name;
            $info['admin_note'] = $admin_note;
            $info['done_date'] = $done_date;
            if ($done) $info['cr'] = '';
            else $info['cr'] = $cr ? '<br> يرجى الالغاء : '.data::decode($cr)['note'] : '';

            return $html->part($stat+1)->fill($info);
        } else {
            return $html->part(1);
        }
    }

    public function can_view() {
        // not a bill
        $this->decode();
        if (!$this->id) return false;
        // same sender or user_id or admin_id
        $id = member::$current->id;
        if (!$id) return false;
        if ($id == $this->user_id || $id == $this->sender_id || $id== $this->admin_id) return true;
        // or can do nothing ?
        if (!allowed('can_do_bills')) return false;
        // or can do it (ex: mobile_retail_staff)
        $per = $this->script.'_staff';
        return allowed($per);

    }
    static function balance_set($user_id) {
        if (!$user_id) return false;
        $sql = "WHERE user_id = ".$user_id." AND ba = 1";
        $bal = -db::$db->adv_select('SUM(price) as bal','bills',$sql)->first()['bal'];
        //if ($bal === null)
        //if (!$bal) die(db::$db->get_last_error());
        return db::$db->update('members',['balance' => $bal],'id','=',$user_id);
    }

    public function reload()
    {
        $this->load_bill($this->id);
        return $this;
    }
}