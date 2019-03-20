<?php

class LFileLogWriter implements LILogWriter {
    
    private $my_log_file = null;
    private $my_log_format = null;
    private $my_log_type = null;
    private $my_log_mode = null;
    private $max_size_bytes = null;
    
    private $my_log_dir = null;
    private $my_filename = null;
    
    function __construct($log_dir,$filename,$log_type,$log_format,string $log_mode,$max_mb=10) {
        if ($log_dir==null)
            throw new \Exception("Log dir can't be null");
        if ($filename==null)
            throw new \Exception("Log filename can't be null");
        if (!is_writable($log_dir))
            throw new \Exception("Log directory is not writable : ".$log_dir);
        $this->my_log_dir = $log_dir;
        $this->my_filename = $filename;
        
        $this->my_log_file = $log_dir.$filename;
        if ($log_type==null || !in_array($log_type, ['distinct-file','together-file']))
            throw new \Exception('Invalid log type.');
        $this->my_log_type = $log_type;
        
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
        if ($this->my_log_mode==self::MODE_RESET && file_exists($this->my_log_file)) {
            @unlink($this->my_log_file);
        }
    }
    
    private function formatLog($message,$level) {
        $my_date = date(LConfig::mustGet('/defaults/logging/'.$this->my_log_type.'/date_format'));
        
        switch ($level) {
            case self::LEVEL_DEBUG : $level_string = 'debug';break;
            case self::LEVEL_INFO : $level_string = 'info';break;
            case self::LEVEL_WARNING : $level_string = 'warning';break;
            case self::LEVEL_ERROR : $level_string = 'error';break;
            case self::LEVEL_FATAL : $level_string = 'fatal';break;
            default : $level_string = 'unknown';break;
        }
        
        $format = $this->my_log_format;
        $format = str_replace('%date', $my_date, $format);
        $format = str_replace('%level_string', $level_string, $format);
        $format = str_replace('%level', $level, $format);
        $format = str_replace('%user', LEnvironmentUtils::getServerUser(), $format);
        $format = str_replace('%route', LEnvironmentUtils::getRoute(), $format);
        $format = str_replace('%message', $message, $format);
        
        return $format;
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
        file_put_contents($this->my_log_file, $this->formatLog($message, $level), FILE_APPEND | LOCK_EX);
    }
    
    public function close() {
        if ($this->my_log_mode==self::MODE_ROLLING && file_exists($this->my_log_file)) {
            $this->checkSizeAndRoll();
        }
    }
    
} 
