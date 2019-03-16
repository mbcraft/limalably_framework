<?php

class LTogetherFileLog implements LILogger {
    
    const TOGETHER_FILENAME = 'log.all.txt';
    
    private $my_log_writer = null;
    
    function __construct($log_dir,$log_format,$log_mode,$max_mb) {
        $this->my_log_writer = new LFileLogWriter($log_dir, self::TOGETHER_FILENAME, $log_format,$log_mode,$max_mb);
    }
    
    public function init() {
        $this->my_log_writer->init();
    }
    
    public function debug($message) {
        $this->my_log_writer->write($message, 'debug');
    }

    public function error($message) {
        $this->my_log_writer->write($message, 'error');
    }

    public function exception(\Exception $ex) {
        $this->my_log_writer->write(LStringUtils::getExceptionMessage($ex), 'error');
    }

    public function fatal($message) {
        $this->my_log_writer->write($message, 'fatal');
    }

    public function info($message) {
        $this->my_log_writer->write($message, 'info');
    }

    public function warning($message) {
        $this->my_log_writer->write($message, 'warning');
    }
    
    public function close() {
        $this->my_log_writer->close();
    }

}
