<?php

class LRollingFileLogger {
    
    private $my_log_file = null;
    private $my_log_format = null;
    private $is_rolling = null;
    private $max_size_bytes = null;
    
    function __construct($log_dir,$filename,$log_format,bool $is_rolling=true,$max_mb=10) {
        if ($log_dir==null)
            throw new \Exception("Log dir can't be null");
        if ($filename==null)
            throw new \Exception("Log filename can't be null");
        if (!is_writable($log_dir))
            throw new \Exception("Log directory is not writable : ".$log_dir);
        
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
    
    public function write($message) {
        file_put_contents($this->my_log_file, $message, FILE_APPEND | LOCK_EX);
        $this->checkSizeAndRoll();
    }
    
    private static function getExceptionMessage(\Exception $ex) {
        $message = 'Exception : '.$ex->getMessage()."\n";
        $message .= 'File : '.$ex->getFile().' Line : '.$ex->getLine()."\n";
        $message .= 'Stack Trace : '.$ex->getTraceAsString();
        return $message;
    }
    
    public function exception(\Exception $ex) {
        $exceptions = [$ex];
        while ($ex->getPrevious()!=null) {
            $ex = $ex->getPrevious();
            array_unshift ($exceptions, $ex);
        }
        
        foreach ($exceptions as $ex) {
            $this->write_exception($ex);
        }
        
    }
    
    private function write_exception(\Exception $ex) {
        $message = self::getExceptionMessage($ex);
        $this->write($message);
    }
} 
