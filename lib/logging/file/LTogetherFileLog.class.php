<?php

class LTogetherFileLog implements LILogger {
    
    const TOGETHER_FILENAME = 'log.all.txt';
    
    private $my_log_writer = null;
    
    function __construct($log_dir,$log_format,$log_mode,$max_mb=10) {
        if (!file_exists($log_dir) && is_dir($log_dir)) {
            mkdir($log_dir);
            chmod($log_dir, 0775);
        }
        
        $this->my_log_writer = new LFileLogWriter($log_dir, self::TOGETHER_FILENAME, $log_format,$log_mode,$max_mb);
    }
    
    public function init() {
        $this->my_log_writer->init();
    }
    
    public function debug($message) {
        $this->my_log_writer->write($message, self::LEVEL_DEBUG);
    }

    public function error($message) {
        $this->my_log_writer->write($message, self::LEVEL_ERROR);
    }

    public function exception(\Exception $ex) {
        $this->my_log_writer->write(LStringUtils::getExceptionMessage($ex), self::LEVEL_ERROR);
    }

    public function fatal($message) {
        $this->my_log_writer->write($message, self::LEVEL_FATAL);
    }

    public function info($message) {
        $this->my_log_writer->write($message, self::LEVEL_INFO);
    }

    public function warning($message) {
        $this->my_log_writer->write($message, self::LEVEL_WARNING);
    }
    
    public function close() {
        $this->my_log_writer->close();
    }

}
