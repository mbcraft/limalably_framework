<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

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
        } else
            throw new \Exception("Invalid connection, can't get handle.");
    }

    public function isOpen() {
        return $this->is_open;
    }

    public function getConnectionString($params) {
        if (!isset($params['host']))
            throw new \Exception("Database host parameter is not set!");
        $host = $params['host'];

        $port = isset($params['port']) ? $this->params['port'] : 3306;

        if (!isset($params['username']))
            throw new \Exception("Database username parameter is not set!");

        $username = $params['username'];

        if (!isset($params['password']))
            throw new \Exception("Database password parameter is not set!");

        $password = $params['password'];

        if (!isset($this->params['db_name']))
            throw new \Exception("Database db_name parameter is not set!");

        $db_name = $params['db_name'];

        $conn_string = 'mysql:host=' . $host . ';';
        if ($port != 3306) {
            $conn_string .= 'port=' . $port . ';';
        }
        $conn_string .= 'dbname=' . $db_name . ';';

        return $conn_string;
    }

    public function open() {

        try {

            if (!isset($this->params['username']))
                throw new \Exception("Database username parameter is not set!");

            $username = $this->params['username'];

            if (!isset($this->params['password']))
                throw new \Exception("Database password parameter is not set!");

            $password = $this->params['password'];

            $result = new PDO($this->getConnectionString($this->params), $username, $password);

            if ($result) {
                $this->my_handle = $result;
                $this->is_open = true;

                LQueryFunctions::useMysqlLayer();

                return true;
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            LErrorList::saveFromException('db', $ex);
            return false;
        }
    }

}
