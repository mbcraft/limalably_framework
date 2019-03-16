<?php

class LDistinctFileLog implements LILogger {
    
    const DEBUG_FILENAME = 'log.debug.txt';
    const INFO_FILENAME = 'log.info.txt';
    const WARNING_FILENAME = 'log.warning.txt';
    const ERROR_FILENAME = 'log.error.txt';
    const FATAL_FILENAME = 'log.fatal.txt';
    
    private $debug_log_writer = null;
    private $info_log_writer = null;
    private $warning_log_writer = null;
    private $error_log_writer = null;
    private $fatal_log_writer = null;
    
    function __construct($log_dir,$log_format,$log_mode,$max_mb=10) {
    
        $this->debug_log_writer = new LFileLogWriter($log_dir, self::DEBUG_FILENAME, $log_format,$log_mode,$max_mb);
        $this->info_log_writer = new LFileLogWriter($log_dir, self::INFO_FILENAME, $log_format,$log_mode,$max_mb);
        $this->warning_log_writer = new LFileLogWriter($log_dir, self::WARNING_FILENAME, $log_format,$log_mode,$max_mb);
        $this->error_log_writer = new LFileLogWriter($log_dir, self::ERROR_FILENAME, $log_format,$log_mode,$max_mb);
        $this->fatal_log_writer = new LFileLogWriter($log_dir, self::FATAL_FILENAME, $log_format,$log_mode,$max_mb);
        
    }
    
    public function init() {
        $this->debug_log_writer->init();
        $this->info_log_writer->init();
        $this->warning_log_writer->init();
        $this->error_log_writer->init();
        $this->fatal_log_writer->init();
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