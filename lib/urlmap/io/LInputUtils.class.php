<?php

class LInputUtils {
    
    
    static function create() {
        $source_list = LConfigReader::simple('/request/input_source_list');
                
        $import_list = [];
        
        foreach ($source_list as $source) {
            switch ($source) {
                case 'files' : $import_list[] = $_FILES; break;
                case 'post' : $import_list[] = $_POST; break;
                case 'get' : $import_list[] = $_GET; break;
                default : throw new \Exception("Unrecognized input source : ".$source);
            }
        }
        
        $result = [];
        
        foreach ($import_list as $import) {
            $result = array_replace_recursive($result,$import);
        }
        
        $tree_map = new LTreeMap($result);
        return $tree_map->view('/');
    }
}