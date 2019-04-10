<?php

class LJsonDataStorage implements LIDataStorage {
    
    private $root_path = null;
    
    function isInitialized() {
        return $this->root_path != null;
    }
    
    function initWithDefaults() {
        $this->root_path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/misc/data_folder');
    }
    
    function init($root_path) {
        $this->root_path = $root_path;
    }
    
    function load(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.json';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        $content = file_get_contents($my_path1);
        
        return LJsonUtils::parseContent("data file",$path,$content);
    }
    
    function is_saved(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.json';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        //add xml support
        
        return is_file($my_path1);
    }
    
    function save(string $path,array $data) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.json';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        $my_dir = dirname($my_path1);
        if (!is_dir($my_dir)) {
            mkdir($my_dir, 0777, true);
            chmod($my_dir, 0777);
        }
        
        $content = LJsonUtils::encodeData("data file",$path,$data);
        
        file_put_contents($my_path1, $content, LOCK_EX);
    }
    
    function delete(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.json';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        if (is_file($my_path1)) @unlink($my_path1);
    }
    
    
}
