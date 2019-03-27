<?php

class LLog {
    
    static $my_logger = null;
    static $my_logger_initialized = false;
    static $my_min_level = null;
    
    const LEVEL_DEBUG = 1;
    const LEVEL_INFO = 2;
    const LEVEL_WARNING = 3;
    const LEVEL_ERROR = 4;
    const LEVEL_FATAL = 5;
        
    private static function adjustLogFolder($log_folder) {
        if (!LStringUtils::startsWith($log_folder, '/')) {
            return $_SERVER['PROJECT_DIR'].$log_folder;
        } else {
            return $log_folder;
        }
    }
    
    static function init() {
        self::$my_logger = new LOutputLogger();
        self::$my_min_level = self::LEVEL_DEBUG;
    }
    
    static function initWithConfig() {
        $exec_mode = LExecutionMode::get();
        if (!isset($_SERVER['PROJECT_DIR'])) {
            $logger_type = 'output';
        } else {
            $logger_type = LConfigReader::mustExecutionMode('/logging/type');
        }
        self::$my_min_level = LConfigReader::mustExecutionModeWithType($logger_type, '/logger/%type%/min_level');
        
                
        switch ($logger_type) {
            case 'output' : {
                self::$my_logger = new LOutputLogger();
                self::$my_logger_initialized = false;
                break;
            }
            
            case 'distinct-file' : {
                $log_mode = LConfigReader::mustExecutionModeWithType( $logger_type, '/logger/%type%/log_mode');
                $log_folder = LConfigReader::mustExecutionModeWithType($logger_type, '/logger/%type%/log_folder');
                $log_format = LConfigReader::mustExecutionModeWithType( $logger_type, '/logger/%type%/log_format');
                $date_format = LConfigReader::mustExecutionModeWithType($logger_type, '/logger/%type%/date_format');
                $max_mb = LConfigReader::mustExecutionModeWithType( $logger_type, '/logger/%type%/max_mb');
        
                $format_info = ['log' => $log_format,'date' => $date_format];
                
                $log_folder_ok = self::adjustLogFolder($log_folder);
                
                self::$my_logger = new LDistinctFileLogger($log_folder_ok, $format_info, $log_mode,$max_mb);
                self::$my_logger_initialized = false;
                break;
            }
            case 'together-file' : {
                $log_mode = LConfigReader::mustExecutionModeWithType( $logger_type, '/logger/%type%/log_mode');
                $log_folder = LConfigReader::mustExecutionModeWithType( $logger_type, '/logger/%type%/log_folder');
                $log_format = LConfigReader::mustExecutionModeWithType( $logger_type, '/logger/%type%/log_format');
                $date_format = LConfigReader::mustExecutionModeWithType( $logger_type, '/logger/%type%/date_format');
                $max_mb = LConfigReader::mustExecutionModeWithType( $logger_type, '/logger/%type%/max_mb'); 
                
                $format_info = ['log' => $log_format,'date' => $date_format];
                
                $log_folder_ok = self::adjustLogFolder($log_folder);
                
                self::$my_logger = new LTogetherFileLogger($log_folder_ok, $format_info, $log_mode,$max_mb);
                self::$my_logger_initialized = false;
                break;
            }
            case 'db' : { 
                $log_mode = LConfigReader::mustExecutionModeWithType( $logger_type, '/logger/%type%/log_mode');
                $connection_name = LConfigReader::mustExecutionModeWithType( $logger_type, '/logger/%type%/connection_name');
                $max_records = LConfigReader::mustExecutionModeWithType($logger_type, '/logger/%type%/max_records');
                $table_name = LConfigReader::mustExecutionModeWithType($logger_type, '/logger/%type%/table_name');
                
                self::$my_logger = new LDbLogger($connection_name,$log_mode,$max_records,$table_name);
                self::$my_logger_initialized = false;
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
    
    static function isDebug() {
        return self::$my_min_level!=null && self::$my_min_level<=self::LEVEL_DEBUG;
    }
    
    static function debug($message) {    
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger_initialized) {
            self::$my_logger->init();
            self::$my_logger_initialized = true;
        }
        
        self::$my_logger->debug($message);
    }
    
    static function isInfo() {
        return self::$my_min_level!=null && self::$my_min_level<=self::LEVEL_INFO;
    }
    
    static function info($message) {       
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger_initialized) {
            self::$my_logger->init();
            self::$my_logger_initialized = true;
        }
        
        self::$my_logger->info($message);
    }
    
    static function isWarning() {
        return self::$my_min_level!=null && self::$my_min_level<=self::LEVEL_WARNING;
    }
    
    static function warning($message) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger_initialized) {
            self::$my_logger->init();
            self::$my_logger_initialized = true;
        }
        
        self::$my_logger->warning($message);
    }
    
    static function isError() {
        return self::$my_min_level!=null && self::$my_min_level<=self::LEVEL_ERROR;
    }
    
    static function error($message) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger_initialized) {
            self::$my_logger->init();
            self::$my_logger_initialized = true;
        }
        
        self::$my_logger->error($message);
    }
    
    static function exception(\Exception $ex) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger_initialized) {
            self::$my_logger->init();
            self::$my_logger_initialized = true;
        }
        
        self::$my_logger->exception($ex);
    }
    
    static function isFatal() {
        return self::$my_min_level!=null && self::$my_min_level<=self::LEVEL_FATAL;
    }
    
    static function fatal($message) {
        if (self::$my_logger==null) throw new \Exception("Trying to log without a configured logger.");
        
        if (!self::$my_logger_initialized) {
            self::$my_logger->init();
            self::$my_logger_initialized = true;
        }
        
        self::$my_logger->fatal($message);
    }
    
    static function close() {
        self::$my_logger->close();
        self::$my_logger = null;
    }
    
}
