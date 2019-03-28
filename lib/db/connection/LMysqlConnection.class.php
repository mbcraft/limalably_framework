<?php

class LMysqlConnection implements LIDbConnection {

    private $params = null;
    private $is_open = false;
    private $my_handle = null;

    function __construct($params) {
        $this->params = $params;
    }

    public function close() {
        if ($this->is_open) {
            mysqli_close($this->my_handle);
            $this->is_open = false;
        }
    }

    public function getHandle() {
        if ($this->my_handle) {
            return $this->my_handle;
        } else throw new \Exception("Invalid connection, can't get handle.");
    }

    public function isOpen() {
        return $this->is_open;
    }

    public function open() {
        
        try {
            $host = $this->params->mustGet('host');
            $port = $this->params->get('port',3306);
            $username = $this->params->mustGet('username');
            $password = $this->params->mustGet('password');
            $db_name = $this->params->mustGet('db_name');
       
            $result = mysqli_connect($host.':'.$port,$username,$password,$db_name);
            
            if ($result) {
                $this->my_handle = $result;
                $this->is_open = true;
                return true;
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            LResult::exception($ex);
            return false;
        }
    }

}
