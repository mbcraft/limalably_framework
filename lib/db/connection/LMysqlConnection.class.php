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
            $this->my_handle = null;
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
            $host = $this->params['host'];
            $port = isset($this->params['port']) ? $this->params['port'] : 3306;
            $username = $this->params['username'];
            $password = $this->params['password'];
            $db_name = $this->params['db_name'];
       
            $conn_string = 'mysql:host='.$host.';';
            if ($port!=3306) {
                $conn_string.= 'port='.$port.';';
            }
            $conn_string.= 'dbname='.$db_name.';';
            
            $result = new PDO($conn_string,$username,$password);
            
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
