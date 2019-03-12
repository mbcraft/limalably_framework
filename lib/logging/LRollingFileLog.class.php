<?php

class LRollingFileLog {
    
    private $my_log_file = null;
    private $max_size_bytes = null;
    
    function __construct($file_path,$max_mb=10) {
        if (!is_writable(dirname($file_path)))
            throw new \Exception("Log directory is not writable : ".dirname($file_path));
        $this->my_log_file = $file_path;
        if ($max_mb==null) throw new \Exception("Max mb can't be null or 0.");
        if ($max_mb>100) throw new \Exception("Max mb of log file can't be greater than 100 and less than or equal to 0.");
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
