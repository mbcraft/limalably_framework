<?php

class LSqliteConnection implements LIDbConnection {
    
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
            $filename = $this->params['filename'];
            if (!LStringUtils::startsWith($filename, '/')) {
                $filename = LConfig::mustGet('PROJECT_DIR').$filename;
            }
            
            $result = new PDO('sqlite:'.$filename);
            
            $this->my_handle = $result;
            return true;
            
        } catch (\Exception $ex) {
            LResult::exception($ex);
            return false;
        }
    }

}
