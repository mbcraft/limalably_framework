<?php

class LDistinctFileLog implements LILogger {
    
    private $debug_log_writer = null;
    private $info_log_writer = null;
    private $warning_log_writer = null;
    private $error_log_writer = null;
    private $fatal_log_writer = null;
    
    function __construct($log_dir) {
    
        $log_format;
    
        $this->debug_log_writer = new LRollingFileLogWriter($log_dir, 'log.debug.txt', $log_format);
        $this->info_log_writer = new LRollingFileLogWriter($log_dir, 'log.info.txt', $log_format);
        $this->warning_log_writer = new LRollingFileLogWriter($log_dir, 'log.warning.txt', $log_format);
        $this->error_log_writer = new LRollingFileLogWriter($log_dir, 'log.error.txt', $log_format);
        $this->fatal_log_writer = new LRollingFileLogWriter($log_dir, 'log.fatal.txt', $log_format);
        
    }
    
    public function debug($message) {
        $this->debug_log_writer->write($message, 'debug');
    }

    public function error($message) {
        $this->error_log_writer->write($message, 'error');
    }

    public function exception(\Exception $ex) {
        $this->error_log_writer->write(LStringUtils::getExceptionMessage($ex), 'error');
    }

    public function fatal($message) {
        $this->fatal_log_writer->write($message, 'fatal');
    }

    public function info($message) {
        $this->info_log_writer->write($message, 'info');
    }

    public function warning($message) {
        $this->warning_log_writer->write($message, 'warning');
    }
    
    public function close() {
        $this->debug_log_writer->close();
        $this->info_log_writer->close();
        $this->warning_log_writer->close();
        $this->error_log_writer->close();
        $this->fatal_log_writer->close();
    }
}