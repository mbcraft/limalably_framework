<?php

class LHashDbUtils {
    
    function listRoutes() {
        $base_dir = LEnvironmentUtils::getBaseDir();
        
        $hash_db_folder = LConfigReader::simple('/urlmap/hash_db_routes_folder');
        
        $final_hash_db_folder = $base_dir.$hash_db_folder;
        
        $elements = scandir($final_hash_db_folder);
        
        $real_elements = [];
        
        foreach ($elements as $el) {
            if ($el=='.' || $el == '..') continue;
            $real_elements[] = $el;
        }
        
        $entries = [];
        
        foreach ($real_elements as $element) {
            $content = file_get_contents($final_hash_db_folder.$element);
        
            try {
                $array_data = LJsonUtils::parseContent('urlmap', $final_hash_db_folder.$element, $content);
            } catch (\Exception $ex) {
                $array_data = [];
            }
            
            if (isset($array_data['real_url'])) {
                $entries[] = $array_data['real_url'];
            } else {
                $entries[] = "-- Invalid hash db entry --";
            }
        }
        
        return $entries;
    }
    
    function addRoute($public_route_name,$wanted_route_name) {
        
        $route_resolver = new LUrlMapResolver();
        
        if (!$route_resolver->isStaticRoute($public_route_name)) return "Unable to find public route : ".$public_route_name;
        
        $builder = new LUrlMapBuilder();
        $builder->setExtends($public_route_name);
        $builder->setRealUrl($wanted_route_name);
        
        $base_dir = LEnvironmentUtils::getBaseDir();
        
        $hash_db_folder = LConfigReader::simple('/urlmap/hash_db_routes_folder');
        
        $final_filename = $base_dir.$hash_db_folder.$route_resolver->getHashDbFilename($wanted_route_name);
        
        $builder->writeToFile($final_filename);
        
        return "Entry added.";
        
    }
    
    public function removeRouteByIndex($index) {
        
        if (!is_numeric($index)) return "It is necessary to provide a numeric index.";
        
        if ($index<0) return "The numeric index must be greater or equal than zero.";
        
        $base_dir = LEnvironmentUtils::getBaseDir();
        
        $hash_db_folder = LConfigReader::simple('/urlmap/hash_db_routes_folder');
        
        $final_hash_db_folder = $base_dir.$hash_db_folder;
        
        $elements = scandir($final_hash_db_folder);
        
        $real_elements = [];
        
        foreach ($elements as $el) {
            if ($el=='.' || $el == '..') continue;
            $real_elements[] = $el;
        }
        
        if (count($real_elements)<=$index) return "The numeric index must not be greater than the maximum number of elements.";
        
        $my_element = $real_elements[$index];
        
        $content = file_get_contents($final_hash_db_folder.$my_element);
        
        try {
            $array_data = LJsonUtils::parseContent('urlmap', $final_hash_db_folder.$my_element, $content);
        } catch (\Exception $ex) {
            return "The provided index does not point to a valid hash_db entry : invalid json file";
        }
        
        if ($array_data['real_url']) {
            @unlink($final_hash_db_folder.$my_element);
            
            return "Deleted entry ".$index." for url : ".$array_data['real_url'];
        } else {
            return "The provided index does not point to a valid hash_db entry : missing real_url key";
        }
        
    }
    
}
