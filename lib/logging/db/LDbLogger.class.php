<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LDbLogger implements LILogger {
    
    const CONNECTION_TYPE_MYSQL = 'mysql';
    const CONNECTION_TYPE_SQLITE = 'sqlite';
    
    private $initialized = false;
    
    private $my_logger;
    
    function __construct($connection_name,$log_mode, $max_records = 1000000, $table_name = 'logs') {
        
        $params = LConfigReader::simple('/database/'.$connection_name.'/');
        if (!isset($params['type']))
            throw new \Exception("Database type is not set in database params.");
        $type = $params['type'];
        switch ($type) {
            case self::CONNECTION_TYPE_MYSQL : $this->my_logger = new LMysqlLogWriter ($connection_name, $log_mode, $max_records, $table_name);break;
        
            default : throw new \Exception("Unsupported connection type for db logging : ".$type);
        }
        
    }
    
    public function close() {
        $this->my_logger->close();
    }

    public function debug($message) {
        $this->my_logger->write($message,self::LEVEL_DEBUG);
    }

    public function error($message,$code = '') {
        $this->my_logger->write($message,self::LEVEL_ERROR,$code);
    }

    public function exception(\Exception $ex) {
        $this->my_logger->write(LStringUtils::getExceptionMessage($ex),self::LEVEL_ERROR,$ex->getCode());
    }

    public function fatal($message) {
        $this->my_logger->write($message,self::LEVEL_FATAL);
    }

    public function info($message) {
        $this->my_logger->write($message,self::LEVEL_INFO);
    }

    public function isInitialized() {
        return $this->initialized;
    }
    
    public function init() {
        $this->initialized = true;
        
        $this->my_logger->init();
    }

    public function warning($message) {
        $this->my_logger->write($message,self::LEVEL_WARNING);
    }

}
