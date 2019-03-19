<?php

class LDbLog implements LILogger {
    
    private $my_logger;
    
    function __construct($connection_name,$log_mode) {
        
    }
    
    public function close() {
        $this->my_logger->close();
    }

    public function debug($message) {
        $this->my_logger->write($message,self::LEVEL_DEBUG);
    }

    public function error($message) {
        $this->my_logger->write($message,self::LEVEL_ERROR);
    }

    public function exception(\Exception $ex) {
        $this->my_logger->write(LStringUtils::getExceptionMessage($ex),self::LEVEL_ERROR);
    }

    public function fatal($message) {
        $this->my_logger->write($message,self::LEVEL_FATAL);
    }

    public function info($message) {
        $this->my_logger->write($message,self::LEVEL_INFO);
    }

    public function init() {
        $this->my_logger->init();
    }

    public function warning($message) {
        $this->my_logger->write($message,self::LEVEL_WARNING);
    }

}
