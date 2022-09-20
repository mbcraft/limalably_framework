<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LIniDataStorage extends LAbstractDataStorage implements LIDataStorage {
    
    protected function getFileExtension() {
        return ".ini";
    }
    
    public function loadArray(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (LStringUtils::endsWith($path, '.ini')) {
            $my_path1 = $this->root_path.$path;
        }
        else {
            $my_path1 = $this->root_path.$path.'.ini';
        }
        $my_path1 = str_replace('//', '/', $my_path1);
        
        $result = parse_ini_string(file_get_contents($my_path1), false, INI_SCANNER_RAW);
        
        if ($result===false) LErrorList::saveFromErrors ('ini', "Error parsing ini file : ".$my_path1.". The data is not valid. Use \" to delimit strings.");
        
        return $result;
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
        $ini_lines = [];

        $my_data = [];

        $this->recursiveFlatDataIntoTreePath($my_data,$data,"");

        foreach ($my_data as $k => $v) {
            $ini_lines [] = $k." = ".$v."\n";
        }

        $content = implode("",$ini_lines);

        file_put_contents($this->prepareAndGetStorageFilePath($path), $content, LOCK_EX);
    }

}
