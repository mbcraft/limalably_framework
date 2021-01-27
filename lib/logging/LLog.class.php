<?php

class LLog {
    
    static $init_called = false;
    
    static $my_logger = null;

    static $my_min_level = null;
    
    const LEVEL_DEBUG = 1;
    const LEVEL_INFO = 2;
    const LEVEL_WARNING = 3;
    const LEVEL_ERROR = 4;
    const LEVEL_FATAL = 5;
        
    private static function adjustLogFolder($log_folder) {
        // added fix for windows folders
        if (!LStringUtils::startsWith($log_folder, '/') && $log_folder[1]!=':') {
            return $_SERVER['PROJECT_DIR'].$log_folder;
        } else {
            return $log_folder;
        }
    }
    
    static function init() {
        if (self::$init_called) return;
        self::$init_called = true;
        
        self::$my_logger = new LResultLogger();
        self::$my_min_level = self::LEVEL_DEBUG;

        if (!isset($_SERVER['PROJECT_DIR'])) {
            $logger_type = 'output';
        } else {
            $logger_type = LConfigReader::executionMode('/logging/type','output');
        }
        self::$my_min_level = LConfigReader::executionModeWithType($logger_type, '/logging/%type%/min_level');
        
                
        switch ($logger_type) {
            case 'output' : {
                self::$my_logger = new LResultLogger();
                break;
            }
            
            case 'distinct-file' : {
                $log_mode = LConfigReader::executionModeWithType( $logger_type, '/logging/%type%/log_mode');
                $log_folder = LConfigReader::executionModeWithType($logger_type, '/logging/%type%/log_folder');
                $log_format = LConfigReader::executionModeWithType( $logger_type, '/logging/%type%/log_format');
                $date_format = LConfigReader::executionModeWithType($logger_type, '/logging/%type%/date_format');
                $max_mb = LConfigReader::executionModeWithType( $logger_type, '/logging/%type%/max_mb');
        
                $format_info = ['log' => $log_format,'date' => $date_format];
                
                $log_folder_ok = self::adjustLogFolder($log_folder);
                
                self::$my_logger = new LDistinctFileLogger($log_folder_ok, $format_info, $log_mode,$max_mb);
                break;
            }
            case 'together-file' : {
                $log_mode = LConfigReader::executionModeWithType( $logger_type, '/logging/%type%/log_mode');
                $log_folder = LConfigReader::executionModeWithType( $logger_type, '/logging/%type%/log_folder');
                $log_format = LConfigReader::executionModeWithType( $logger_type, '/logging/%type%/log_format');
                $date_format = LConfigReader::executionModeWithType( $logger_type, '/logging/%type%/date_format');
                $max_mb = LConfigReader::executionModeWithType( $logger_type, '/logging/%type%/max_mb'); 
                
                $format_info = ['log' => $log_format,'date' => $date_format];
                
                $log_folder_ok = self::adjustLogFolder($log_folder);
                
                self::$my_logger = new LTogetherFileLogger($log_folder_ok, $format_info, $log_mode,$max_mb);
                break;
            }
            case 'db' : { 
                $log_mode = LConfigReader::executionModeWithType( $logger_type, '/logging/%type%/log_mode');
                $connection_name = LConfigReader::executionModeWithType( $logger_type, '/logging/%type%/connection_name');
                $max_records = LConfigReader::executionModeWithType($logger_type, '/logging/%type%/max_records');
                $table_name = LConfigReader::executionModeWithType($logger_type, '/logging/%type%/table_name');
                LResult::trace("Creating instance of dblogger ...");
                self::$my_logger = new LDbLogger($connection_name,$log_mode,$max_records,$table_name);
                break;
            }
        }
        
        
    }
    
    static function getLevel() {
        return self::$my_min_level;
    }
    
    static function getLevelString() {
        switch (self::$my_min_level) {
            case self::LEVEL_DEBUG:return 'debug';
            case self::LEVEL_INFO:return 'info';
            case self::LEVEL_WARNING:return 'warning';
            case self::LEVEL_ERROR:return 'error';
            case self::LEVEL_FATAL:return 'fatal';
            default: throw new \Exception('Invalid level value : '.self::$my_min_level);
        }
    }
    
    public static function isDebug() {
        return self::$my_min_level!=null && self::$my_min_level<=self::LEVEL_DEBUG;
    }
    
    public static function debug($message) {    
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger->isInitialized()) {
            self::$my_logger->init();
        }
        
        self::$my_logger->debug($message);
    }
    
    public static function isInfo() {
        return self::$my_min_level!=null && self::$my_min_level<=self::LEVEL_INFO;
    }
    
    public static function info($message) {       
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger->isInitialized()) {
            self::$my_logger->init();
        }
        
        self::$my_logger->info($message);
    }
    
    public static function isWarning() {
        return self::$my_min_level!=null && self::$my_min_level<=self::LEVEL_WARNING;
    }
    
    public static function warning($message) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger->isInitialized()) {
            self::$my_logger->init();
        }
        
        self::$my_logger->warning($message);
    }
    
    public static function isError() {
        return self::$my_min_level!=null && self::$my_min_level<=self::LEVEL_ERROR;
    }
    
    public static function error($message,$code = '') {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger->isInitialized()) {
            self::$my_logger->init();
        }
        
        self::$my_logger->error($message,$code);
    }
    
    public static function exception(\Exception $ex) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger->isInitialized()) {
            self::$my_logger->init();
        }
        
        self::$my_logger->exception($ex);
    }
    
    public static function isFatal() {
        return self::$my_min_level!=null && self::$my_min_level<=self::LEVEL_FATAL;
    }
    
    public static function fatal($message) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger->isInitialized()) {
            self::$my_logger->init();
        }
        
        self::$my_logger->fatal($message);
    }
    
    public static function close() {
        if (self::$my_logger) {
            self::$my_logger->close();
            self::$my_logger = null;
        }
    }
    
}
