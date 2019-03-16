<?php

class LFileLogWriter {
    
    const NORMAL_MODE = 'normal';
    const RESET_MODE = 'reset';
    const ROLLING_MODE = 'rolling';
    
    const LOG_MODES_ARRAY = [self::NORMAL_MODE,self::RESET_MODE,self::ROLLING_MODE];
    
    use LFormatLog;
    
    private $my_log_file = null;
    private $my_log_format = null;
    private $my_log_mode = null;
    private $max_size_bytes = null;
    
    private $my_log_dir = null;
    private $my_filename = null;
    
    function __construct($log_dir,$filename,$log_format,string $log_mode,$max_mb=10) {
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
            $log_mode = 'normal';
        }
        if (!in_array($log_mode, self::LOG_MODES_ARRAY))
                throw new \Exception('Invalid log mode. Allowed values : '.implode(',',self::LOG_MODES_ARRAY));
        $this->my_log_mode = $log_mode;
        $this->max_size_bytes = 1000 * 1000 * $max_mb;
    }
    
    function init() {
        if ($this->my_log_mode==self::RESET_MODE && file_exists($this->my_log_file)) {
            @unlink($this->my_log_file);
        }
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
        if ($this->my_log_mode==self::ROLLING_MODE && file_exists($this->my_log_file)) {
            $this->checkSizeAndRoll();
        }
    }
    
} 
