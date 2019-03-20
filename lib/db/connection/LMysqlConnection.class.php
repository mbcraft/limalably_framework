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
            $server = $this->params->mustGet('server');
            $username = $this->params->mustGet('username');
            $password = $this->params->mustGet('password');
            $dbname = $this->params->mustGet('dbname');
       
            $result = mysqli_connect($server,$username,$password,$dbname);
            
            if ($result) {
                $this->my_handle = $result;
                $this->is_open = true;
                return true;
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            LOutput::exception($ex);
            return false;
        }
    }

}
