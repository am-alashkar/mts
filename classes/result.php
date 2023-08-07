<?php


class result implements ArrayAccess
{
    public function count() : int {
        return count(get_object_vars($this));
    }
    public function is_empty() : bool {
        return $this->count() == 0;
    }
    public function join($data = null,$overwrite = true) {
        if ($data) foreach ($data as $key=>$var) {
            if (!isset($this->$key) || (isset($this->$key) && $overwrite) ) {
                $this->$key = $var;
            }
        }
        return $this;
    }
    public function add($key ,$data = null) {
        $this->$key = $data;
        return $this;
    }
//    public function to_array() {
//        foreach ($this as $key => $var) {
//            $arr[$key] = $var;
//        }
//        return $arr;
//    }
    function __toString(): string
    {
        return $this->count().'';
    }
    function first() {
        foreach ($this as $item) return $item;
    }
    function last() {
        $item = null;
        foreach ($this as $item) ;
        return $item;
    }
    function next($item = null) {
        foreach ($this as $key=>$it) {
            if ($item) yield $this->{$key}[$item];
            else yield $key=>$it;
        }
    }

    public function offsetExists( $offset) : bool
    {
        return isset($this->$offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->$offset;
    }

    public function offsetSet($offset, $value) : void
    {
        if ($offset == '') $offset = $this->count()+1;
        $this->$offset = $value;
    }

    public function offsetUnset($offset) : void
    {
        unset($this->$offset);
    }
}
