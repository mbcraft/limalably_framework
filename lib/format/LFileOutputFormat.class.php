<?php

class LFileOutputFormat {
    
    static $file_save_path = null;
    
    static function setSaveFilePath($file_path) {
        self::$file_save_path = $file_path;
    }
    
    static function begin() {
        ob_start();
    }
    
    static function end() {
        $content = ob_get_clean();
        $directory = dirname(self::$file_save_path);
        if (!is_dir($directory)) {
            mkdir($directory,0777,true);
            chmod($directory,0777);
        }
        
        file_put_contents(self::$file_save_path, $content, LOCK_EX);
    }
    
}
