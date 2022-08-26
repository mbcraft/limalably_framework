<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LJsonDataStorage extends LAbstractDataStorage implements LIDataStorage {
    
    protected function getFileExtension() {
        return ".json";
    }
    
    function loadArray($path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (LStringUtils::endsWith($path, '.json')) {
            $my_path1 = $this->root_path.$path;
        }
        else {
            $my_path1 = $this->root_path.$path.'.json';
        }
        $my_path1 = str_replace('//', '/', $my_path1);
        
        $content = file_get_contents($my_path1);

        $data = LJsonUtils::parseContent("data file",$path,$content);

        $result = array();

        $this->explore_nested_json($data,$result);

        return $result;
    }

    function explore_nested_json($node,&$result,$prefix=null) {

        foreach ($node as $key => $value) {
            if (is_array($value)) {
                $p = $prefix;
                if ($p==null) $p="";
                if ($p!=null) $p.=".";
                $this->explore_nested_json($value,$result,$p.$key);
            } else {
                $result[$prefix.".".$key] = $value;
            }
        }
    }
    
    function load(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (LStringUtils::endsWith($path, '.json')) {
            $my_path1 = $this->root_path.$path;
        }
        else {
            $my_path1 = $this->root_path.$path.'.json';
        }
        $my_path1 = str_replace('//', '/', $my_path1);
        
        $content = file_get_contents($my_path1);
        
        return LJsonUtils::parseContent("data file",$path,$content);
    }
        
    function save(string $path,array $data) {
                
        $content = LJsonUtils::encodeData("data file",$path,$data);
        
        file_put_contents($this->prepareAndGetStorageFilePath($path), $content, LOCK_EX);
    }
        
}
