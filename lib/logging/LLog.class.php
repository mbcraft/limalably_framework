<?php

class LLog {
    
    static $my_logger = null;
    static $my_log_level = null;
    
    const LEVEL_DEBUG = 1;
    const LEVEL_INFO = 2;
    const LEVEL_WARNING = 3;
    const LEVEL_ERROR = 4;
    const LEVEL_FATAL = 5;
    
    static function init() {
        
    }
    
    static function getLevel() {
        return self::$my_log_level;
    }
    
    static function getLevelString() {
        switch (self::$my_log_level) {
            case self::LEVEL_DEBUG:return 'debug';
            case self::LEVEL_INFO:return 'info';
            case self::LEVEL_WARNING:return 'warning';
            case self::LEVEL_ERROR:return 'error';
            case self::LEVEL_FATAL:return 'fatal';
            default: throw new \Exception('Invalid level value : '.self::$my_log_level);
        }
    }
    
    static function isDebug() {
        return self::$my_log_level!=null && self::$my_log_level<=self::LEVEL_DEBUG;
    }
    
    static function debug($message) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        self::$my_logger->debug($message);
    }
    
    static function isInfo() {
        return self::$my_log_level!=null && self::$my_log_level<=self::LEVEL_INFO;
    }
    
    static function info($message) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        self::$my_logger->info($message);
    }
    
    static function isWarning() {
        return self::$my_log_level!=null && self::$my_log_level<=self::LEVEL_WARNING;
    }
    
    static function warning($message) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        self::$my_logger->warning($message);
    }
    
    static function isError() {
        return self::$my_log_level!=null && self::$my_log_level<=self::LEVEL_ERROR;
    }
    
    static function error($message) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        self::$my_logger->error($message);
    }
    
    static function exception(\Exception $ex) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        self::$my_logger->exception($ex);
    }
    
    static function isFatal() {
        return self::$my_log_level!=null && self::$my_log_level<=self::LEVEL_FATAL;
    }
    
    static function fatal($message) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        self::$my_logger->fatal($ex);
    }
    
    static function close() {
        self::$my_logger->close();
        self::$my_logger = null;
    }
    
}
