<?php

class LIniDataStorage implements LIDataStorage {
    
    private $root_path = null;
    
    public function delete(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.ini';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        if (is_file($my_path1)) @unlink($my_path1);
    }

    public function init(string $root_path) {
        $this->root_path = $root_path;
    }

    public function initWithDefaults() {
        $this->root_path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/misc/data_folder');
    }

    public function isInitialized() {
        return $this->root_path!=null;
    }
    
    public function isValidFilename($filename) {
        return LStringUtils::endsWith($filename, '.ini');
    }

    public function isSaved(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.ini';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        //add xml support
        
        return is_file($my_path1);
    }
    
    public function loadArray(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.ini';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        return parse_ini_file($my_path1, false, INI_SCANNER_TYPED);
    }

    public function load(string $path) {
        $result_array = $this->loadArray($path);
        
        $result_tree = new LTreeMap();
        
        foreach ($result_array as $key => $value) {
            $result_tree->set($key, $value);
        }
        
        return $result_tree->getRoot();
    }

    public function save(string $path, array $data) {
        throw new \Exception("Ini data storage save operation is not supported!");
    }

}
