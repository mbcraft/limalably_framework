<?php

class LMysqlLogWriter implements LILogWriter {
    
    private $my_handle;
    private $log_mode;
    private $max_records;
    private $table_name;
    
    const QUERY_DELETE_RECORDS = "DELETE FROM `%table_name%` WHERE level < %level%;";
    const QUERY_COUNT_RECORDS = "SELECT COUNT(*) as records_count FROM `%table_name%`;";
    const QUERY_CREATE_TABLE = "CREATE TABLE IF NOT EXISTS `%table_name%` ( `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `level` MEDIUMINT UNSIGNED NOT NULL , `level_string` VARCHAR(16) NOT NULL , `datetime_created` DATETIME NOT NULL , `route` VARCHAR(256) NOT NULL , `message` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM CHARSET=utf8 COLLATE utf8_unicode_ci COMMENT = 'Table for logs';";
    const QUERY_RESET_TABLE = "TRUNCATE TABLE %table_name%;";
    const QUERY_WRITE_LOG = "INSERT INTO `%table_name%` (`id`, `level`, `level_string`, `datetime_created`, `route`, `message`) VALUES (NULL, '%level%', '%level_string%', NOW(), '%route%', '%message%')";
    
    function __construct($connection_name,$log_mode, $max_records = 1000000 ,$table_name = 'logs') {
        
        $this->my_handle = LDbConnectionManager::get($connection_name);
        $this->log_mode = $log_mode;
        $this->max_records = $max_records;
        $this->table_name = $table_name;
        
    }
    
    public function close() {
        
        //rolling if is to be done
        if ($this->log_mode == self::MODE_ROLLING) {
            
            $query = self::QUERY_COUNT_RECORDS;
            $query = str_replace('%table_name%',$this->table_name,$query);
            $result = mysqli_query($this->my_handle, $query);
            $result_array = $result->fetch_assoc();
            $result->free();
            $records_count = $result_array['records_count'];
            
            if ($records_count>$this->max_records) {
                $query = self::QUERY_DELETE_RECORDS;
                $query = str_replace('%table_name%',$this->table_name,$query);
                $query = str_replace('%level%',self::LEVEL_WARNING,$query);
                mysqli_query($this->my_handle, $query);
            }
        }
    }

    public function init() {
        
        //create table if not exists 
        $query = self::QUERY_CREATE_TABLE;
        $query = str_replace('%table_name%',$this->table_name,$query);
        mysqli_query($this->my_handle, $query);
        
        // truncate table if reset mode
        if ($this->log_mode == self::MODE_RESET) {
            $query = self::QUERY_RESET_TABLE;
            $query = str_replace('%table_name%',$this->table_name,$query);
            
            mysqli_query($this->my_handle, $query);
        }
    }

    public function write($message, $level) {
        //write message into db
        $query = self::QUERY_WRITE_LOG;
        $query = str_replace('%table_name%',$this->table_name,$query);
        $query = str_replace('%level%',mysqli_escape_string($this->my_handle, $level),$query);
        
        switch ($level) {
            case self::LEVEL_DEBUG : $level_string = 'debug';break;
            case self::LEVEL_INFO : $level_string = 'info';break;
            case self::LEVEL_WARNING : $level_string = 'warning';break;
            case self::LEVEL_ERROR : $level_string = 'error';break;
            case self::LEVEL_FATAL : $level_string = 'fatal';break;
            default : $level_string = 'unknown';break;
        }
        
        $query = str_replace('%level_string%', mysqli_escape_string($this->my_handle, $level_string),$query);
        $query = str_replace('%route%', mysqli_escape_string($this->my_handle, LConfig::mustGet('ROUTE')),$query);
        $query = str_replace('%message%', mysqli_escape_string($this->my_handle, $message),$query);
        
        mysqli_query($this->my_handle, $query);
        
    }

}