<?php


class password
{
    private $real_pass,$pass;
    function __construct($password = '')
    {
        if (!$password) $password = data::$get->password;
        $this->real_pass = base64_decode((string) $password);
        $this->pass = $password;
    }
    public function set_password($password) {
        if (!$password) $password = '';
        $this->real_pass = base64_decode($password);
        $this->pass = $password;
    }
    public function length(){
        return iconv_strlen($this->real_pass,'UTF-8');
    }
    public function upperchars() {
        if (preg_match('/[A-Z]/',$this->real_pass)) return true;
        return false;
    }
    public function lowerchars() {
        if (preg_match('/[a-z]/',$this->real_pass)) return true;
        return false;
    }
    public function numerics() {
        if (preg_match('/[0-9]/',$this->real_pass)) return true;
        return false;
    }
    public function sympols() {
        if (preg_match('/[^\w\s]/',$this->real_pass)) return true;
        return false;
    }
    public function hiddenchar() {
        if (preg_match('/\s/',$this->real_pass)) return true;
        return false;
    }
    public function score() {
        $score = 0;
        if ($this->length() > 7) $score++;
        if ($this->lowerchars()) $score++;
        if ($this->upperchars()) $score++;
        if ($this->sympols()) $score++;
        if ($this->numerics()) $score++;
        if ($this->hiddenchar()) $score = 0;
        return $score;
    }
    public function get_hash($pass = null)
    {
        if (!$pass) $pass = $this->real_pass;
        $hash = password_hash($pass, PASSWORD_BCRYPT,[]);
        //$hash = md5($pass).sha1($pass);
        return $hash;
    }

    /**
     * @param result $result
     */
    public function set_hash(result $result) {
        $result->password = $this->get_hash();
        return $result;
    }

    /**
     * @param string $password from input ( already base64_encoded )
     * @param string $chk_hash hash to compare
     * @return bool $password hash == $chk_hash
     */
    public function check($chk_hash) {
        $pass = $this->real_pass;
        return password_verify($pass,$chk_hash);
        //$hash = $this->get_hash($pass);
        //return $hash == $chk_hash;
    }
    static function get_random_password() {
        $a[0] = "abcdefjhjklmnopqrstuvwxyz";
        $a[1] = "1234567890";
        $a[2] = "!@#$%^*()-_=";
        $a[3] = strtoupper($a[0]);
        $i=12;
        $p = '';
        $g = new password(base64_encode($p));
        while ($g->score() < 4 )
        {
            while ($i--){
                $ar = rand(1,600) % 4;
                $lt = rand(1,200*strlen($a[$ar])) % strlen($a[$ar]);
                $p .= $a[$ar][$lt];
            }
            $g->set_password(base64_encode($p));
        }
        return $p;
    }
}