<?php

class LRollingFileLogger {
    
    private $my_log_file = null;
    private $my_log_format = null;
    private $is_rolling = null;
    private $max_size_bytes = null;
    
    private $my_log_dir = null;
    private $my_filename = null;
    
    function __construct($log_dir,$filename,$log_format,bool $is_rolling=true,$max_mb=10) {
        if ($log_dir==null)
            throw new \Exception("Log dir can't be null");
        if ($filename==null)
            throw new \Exception("Log filename can't be null");
        if (!is_writable($log_dir))
            throw new \Exception("Log directory is not writable : ".$log_dir);
        $this->my_log_dir = $log_dir;
        $this->my_filename = $filename;
        
        $this->my_log_file = $log_dir.$filename;
        if ($log_format==null)
            throw new \Exception("Log format can't be null");
        $this->my_log_format = $log_format;
        if ($max_mb==null) {
            $max_mb = 0;
            $is_rolling = false;
        }
        $this->is_rolling = $is_rolling;
        $this->max_size_bytes = 1000 * 1000 * $max_mb;
    }
    
    private function checkSizeAndRoll() {
        $filesize = filesize($this->my_log_file);
        if ($filesize > $this->max_size_bytes) {
            $full_content = file_get_contents($this->my_log_file);
            $full_content = substr($full_content, ($filesize/10)*9);
            file_put_contents($this->my_log_file, $full_content, LOCK_EX);
        }
    }
    
    public function write($message,$level) {
        file_put_contents($this->my_log_file, $message, FILE_APPEND | LOCK_EX);
    }
    
    public function close() {
        if ($this->is_rolling) {
            $this->checkSizeAndRoll();
        }
    }
    
} 
