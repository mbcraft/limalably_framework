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
            sqlite_close($this->my_handle);
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
            $filename = $this->params->mustGet('filename');
            if (!LStringUtils::startsWith($filename, '/')) {
                $filename = LConfig::mustGet('PROJECT_DIR').$filename;
            }
            $error_message = null;
            $result = sqlite_open($filename, 0666, $error_message);
            if ($result) {
                $this->my_handle = $result;
                return true;
            } else {
                throw new \Exception($error_message);
            }
        } catch (\Exception $ex) {
            LResult::exception($ex);
            return false;
        }
    }

}
