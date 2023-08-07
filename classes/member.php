<?php
// session continue ( if logged in )

class member
{
    /**
     * @var member $current;
     */
    // @TODO : trusted browser
    static $current; // current user
    /**
     * @var result[] $cached
     */
    static $cached; // cached members @TODO this
    public $id,$name,$group_id,$group,$lang_name,$message,$session_id,$parent_id,$timezone,$balance,$credit,$on_behalf;
    /**
     * @var array $permissions
     */
    private $permissions;
    private $sid,$password;
    /**
     * @var int
     */
    public $price_group;

    /**
     * return the id that this user uses for bills ( the parent account id )
     *
     * @return int
     */
    static function get_family_id() {
        if (!member::$current->id) return 0;
        return member::$current->parent_id ? member::$current->parent_id : member::$current->id;
    }
    function __construct()
    {
        if(data::$get->job == 'api') {
            return null;
        }
        require 'session.php';
        if ($_SESSION['sess_sid']) {
            // there is a logged in user ?
            $this->load_login();
            if (!$this->check_session()) {
//                    debug::add('login failed old member');
                $this->create_new();
            }
//            if (!$this->load_login()) {
////                debug::add('login failed'.data::$get->job);
//                // old guest
//                if (!$this->check_session()) {
////                    debug::add('check failed'.data::$get->job);
//                    $this->create_new_session();
//                }
//            } else {
//                // old member
////                debug::add('old member '.data::$get->job);
//                if (!$this->check_session()) {
////                    debug::add('login failed old member');
//                    $this->create_new();
//                }
//            }

            $this->set_last_seen();
        } else {
//            debug::add('totally new'.data::$get->job);
            $this->create_new();
        }
        self::$current = $this;
    }
    public function set_last_seen() {
        $up['last_active'] = date(config::$get->storedatetime);
        if ($this->session_id) db::$db->update('sessions',$up,'id','=',$this->session_id);
        // NULL false 0 ""

    }
    private function check_session() {
        $tmp = db::$db->select('*','sessions','sid','=',$_SESSION['sess_sid'])->first();
        $this->session_id = $tmp['id'];
        return is_array($tmp) && ( ( $tmp['user_id'] && $_SESSION['user_id'] == $tmp['user_id']) || (!$tmp['user_id'] && $_SESSION['user_id'] == 0) );
    }
    private function create_new() {
        // NEW GUEST
        $this->id = 0;
        $this->name = '{{guest}}';
        $this->lang_name = '-1';
        $this->group = $this->group_id = null;
        //return $this;
        $this->create_new_session();
    }
    private function create_new_session() {
        $this->sid = sha1(rand(1,9999).time());
        $in['sid'] = $this->sid;
        $in['last_active'] = date(config::$get->storedatetime);
        $this->session_id = db::$db->insert('sessions',$in);
        $_SESSION['sess_sid'] = $this->sid;
        $_SESSION['user_id'] = 0;

    }
    private function set_guest() {
        $this->id = 0;
        $this->name = '{{guest}}';
        $this->lang_name = '-1';
        $this->group = $this->group_id = null;
    }
    private function load_login() {
        // Check if session is correct or not
        // session is in db
//        debug::add('Login 1'.data::$get->job);
        if (!$_SESSION['user_id']) {
            $this->set_guest();
            return false;
        }
//        debug::add('Login 2'.data::$get->job);

        //$user = db::$db->select('*','members','id','=',$_SESSION['user_id'])->first();
        $user = member::load_member($_SESSION['user_id']);
        if ($user->deleted) {
//            debug::add('Login deleted'.data::$get->job);
            $this->message = '{{user_deleted_message}}';
            $this->set_guest();
            return false;
        }
        if ($user->disabled) {
//            debug::add('Login disabled'.data::$get->job);
            $this->message = $user->disable_note;
            $this->set_guest();
            return false;
        }
        if ($user->parent_id) {
            $pm = member::load_member($user->parent_id);
            if ($pm->deleted || $pm->disabled) {
                $this->set_guest();
                return false;
            }
        }
//        debug::add('Login ok');
        foreach ($user as $key=>$value) {
            $this->$key = $value;
        }
        //$this->name = $user['name'];
        //$this->id = $user['id'];
        //$this->password = $user['password'];
        //$this->group_id = $user['group_id'];
        //if ($this->group_id) $this->group = db::$db->select('name','groups','id','=',$user['group_id'])->first()['name'];
        //$this->lang_name = $user['lang_name'];
        if ($this->timezone) {
            /**
             * @todo : why to check ?
             * if error it will generate a notice
             * (warning if without parameter but in this case if null will generate a notice)
             * and will keep using the previous successful one.
             */
            if (timezone_open($this->timezone)) date_default_timezone_set($this->timezone);
        }
        //$_SESSION['lang'] = $this->lang_name;
        if (in_array($this->lang_name, _LANG_LIST_)) {
            $_SESSION['lang'] = $this->lang_name;
        } else {
            $u['lang_name'] = _LANG_;
            db::$db->update('members',$u,'id','=',$this->id);
        }
        //if (!$this->price_group || !is_numeric($this->price_group)) $this->price_group = 3;
        //if ($this->price_group < 1 || $this->price_group > 3) $this->price_group = 3;
        $this->load_permissions();
        return true;
    }

    public static function check() : bool
    {
        return !!self::$current->id;
    }

    public static function get_id()
    {
        return member::$current->id;
    }

    public function allowed($per = null) : bool {
        if (_SUPER_ADMIN_ && $this->id == _SUPER_ADMIN_) return true;
        if (!$per) return false;
        if ($per == 'all') return true;
        if ($this->permissions['u'][$per]['needs']) {
            return !!$this->permissions['u'][$this->permissions['u'][$per]['needs']]['script'] ||
                !!$this->permissions['p'][$this->permissions['u'][$per]['needs']]['script'];
        } else {
            return !!$this->permissions['u'][$per]['script'];
        }
    }
    private function load_permissions() {
        if (!$this->id) return $this;
        $upers = db::$db->select('*','permissions','user_id','=',$this->id);
        if ($this->group_id) $gpers = db::$db->select('*','permissions','group_id','=',$this->group_id);
        if ($upers) foreach ($upers as $uper) {
            if ($uper['only_group']!== null && $uper['only_group'] != $this->group_id) continue;
            $uper['script'] = trim((string) $uper['script']);
            $uper['needs'] = trim((string) $uper['needs']);
            $this->permissions['u'][$uper['script']]['script'] = $uper['script'];
            $this->permissions['u'][$uper['script']]['needs'] = $uper['needs'];
        }
        if ($gpers) foreach ($gpers as $gper) {
            if ($gper['only_group']!== null && $gper['only_group'] != $this->group_id) continue;
            $gper['script'] = trim((string) $gper['script']);
            $gper['needs'] = trim((string) $gper['needs']);
            $this->permissions['u'][$gper['script']]['script'] = $gper['script'];
            $this->permissions['u'][$gper['script']]['needs'] = $gper['needs'];
        }
        if ($this->parent_id) {
            $parent = self::load_member($this->parent_id);
            $upers = db::$db->select('*','permissions','user_id','=',$parent->id);
            if ($parent->group_id) $gpers = db::$db->select('*','permissions','group_id','=',$parent->group_id);
            // permissions here is loaded only if parent is from same group as parent permissions
            if ($upers) foreach ($upers as $uper) {
                if ($uper['only_group']!== null && $uper['only_group'] != $parent->group_id) continue;
                $uper['script'] = trim((string) $uper['script']);
                $uper['needs'] = trim((string) $uper['needs']);
                $this->permissions['p'][$uper['script']]['script'] = $uper['script'];
                $this->permissions['p'][$uper['script']]['needs'] = $uper['needs'];
            }
            if ($gpers) foreach ($gpers as $gper) {
                if ($gper['only_group']!== null && $gper['only_group'] != $parent->group_id) continue;
                $gper['script'] = trim((string) $gper['script']);
                $gper['needs'] = trim((string) $gper['needs']);
                $this->permissions['p'][$gper['script']]['script'] = $gper['script'];
                $this->permissions['p'][$gper['script']]['needs'] = $gper['needs'];
            }
        }
        return $this;
    }

    /**
     * Returns user data by id .. if user is disabled or deleted return result object with deleted = 1 or disabled = 1 without user data
     * @param int $id user id to load
     * @param bool $forced load even if disabled
     * @return result user data includes group
     */
    static function load_member($id = 0,$forced = false) {
        $mem = new result();
        if (!$id || $id < 0) return $mem;
        if (member::$cached[$id]) return member::$cached[$id];
        $user = db::$db->select('*','members','id','=',$id)->first();
        if (!$user) {
            member::$cached[$id] = $mem;
            return $mem;
        }
        $mem->add('id',$id);
        if ($user['deleted']) {
//            debug::add('Login deleted'.data::$get->job);
            $mem->deleted = 1;
            $mem->message = '{{user_deleted_message}}';
            member::$cached[$id] = $mem;
            return $mem;
        }
        if ($user['disabled'] && !$forced) {
//            debug::add('Login disabled'.data::$get->job);
            $mem->disabled = 1;
            $mem->message = '{{user_disabled_message}} : '.$user['disable_note'];
            member::$cached[$id] = $mem;
            return $mem;
        }
        $mem->join($user);
        if ($mem->group_id) $mem->group = db::$db->select('name','groups','id','=',$user['group_id'])->first()['name'];
       // if (!$mem->price_group) $mem->price_group = 3; // 3 retail - 2 whole - 1 vip
        if (!$mem->price_group || !is_numeric($mem->price_group)) $mem->price_group = 3;
        if ($mem->price_group < 1 || $mem->price_group > 3) $mem->price_group = 3;
        if (!$user['disabled']) member::$cached[$id] = $mem;
        return $mem;
    }
    static function load_member_by_login($login = null) {
        if (!$login) return false;
        return db::$db
            ->select(config::$get->login_by.' , id , last_try , password','members'
                ,config::$get->login_by
                ,'='
                ,$login)
            ->first();
    }
    static function login($id = 0) {
        if (!$id) return false;
        $_SESSION['user_id'] = $id;
        if (!member::$current->load_login()) return false;
        $up['user_id'] = $id;
        //$in['sid'] = $_SESSION['sess_sid'];
        $up['last_active'] = date(config::$get->storedatetime);
        db::$db->update('sessions',$up,'id','=',member::$current->session_id);
        //die(db::$db->get_last_error());
        //if () $_SESSION['lang'] =
        session_commit();
        //die('WELCOME '.$id);
        return true;

    }
    public function change_pass($oldpass,$newpass){
        $p = new password($oldpass);
        if (!$p->check($this->password)) return false;
        if (!$newpass) return false;
        if (!$this->id) return false;
        $p = new password($newpass);
        $up['password'] = $p->get_hash();
        return db::$db->update('members',$up,'id','=',$this->id);
    }
    static function logout() {
        $_SESSION['user_id'] = 0;
        session_commit();
        db::$db->update('sessions',['user_id'=>null],'id','=',self::$current->session_id);
        return true;
    }

    /**
     * @param int $id user id
     * @return result Zero based Result object <br>
     * - each item is an array with 3 keys inside 'name','script','needs'
     */
    static function get_permission_list($id = 0) {
        $result = new result();
        if (!$id) return $result;
        $itemList = null;
        $html = new style('admin_permissions_raw');
        // each line : {{name}} script needs
        $user = self::load_member($id);
        $permissions = db::$db->select('*','permissions','user_id','=',$id);
        foreach ($permissions as $permission) {
            $granted[$permission['script']] = true;
        }
        $pers = $html->part($user->group_id)->split("\n");
        $group_name = 'unlisted';
        $i =0 ;
        foreach ($pers as $per) {
            $tmp = explode(' ',$per);
            if ($tmp[0] == '-') $group_name = trim((string) $tmp[1]);
            if ($tmp[0] == '*') {
                $i++;
                $item['name'] = trim((string) $tmp[1]);
                $item['script'] = trim((string) $tmp[2]);
                $item['needs'] = trim((string) $tmp[3]);
                $item['granted'] = !!$granted[trim((string) $tmp[2])];
                $itemList[$group_name][$i] = $item;
            }
            if ($tmp[0] == '**') {
                $sub_item['name'] = trim((string) $tmp[1]);
                $sub_item['script'] = trim((string) $tmp[2]);
                $sub_item['needs'] = trim((string) $tmp[3]);
                $sub_item['granted'] = !!$granted[trim((string) $tmp[2])];
                $itemList[$group_name][$i]['sub'][] = $sub_item;
                //print 'Found'.$sub_item['name'];
            }
        }
        $result->join($itemList);
        return $result;
    }
}