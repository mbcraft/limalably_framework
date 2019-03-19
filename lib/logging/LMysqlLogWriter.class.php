<?php

/* 
 * 
 *  DIMENSIONE DI UNA TABELLA
SELECT 
    table_name AS `Table`, 
    round(((data_length + index_length) / 1024 / 1024), 2) `Size in MB` 
FROM information_schema.TABLES 
WHERE table_schema = ‘db_name’
    AND table_name = ‘table_name’;
 * 
 */

class LMysqlLogWriter implements LILogWriter {
    
    private $connection_name;
    private $log_mode;
    
    function __construct($connection_name,$log_mode) {
        $this->connection_name = $connection_name;
        $this->log_mode = $log_mode;
    }
    
    public function close() {
        //rolling if is to be done
    }

    public function init() {
        //create table if not exists - truncate if reset mode
    }

    public function write($message, $level) {
        //write message into db
    }

}