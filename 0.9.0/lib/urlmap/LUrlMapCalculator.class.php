<?php

class LUrlMapCalculator {
    
    const ROOT_PATH = '/';
    
    private $url_maps_data = [];
         
    public function unshiftUrlMapData($assoc_array) {
        array_unshift($this->url_maps_data,$assoc_array);
    }
    
    public function addUrlMapData($assoc_array) {
        array_push($this->url_maps_data,$assoc_array);
    }
    
    public function calculate() {
                
        $current_data = [];
        foreach ($this->url_maps_data as $url_map_array_data) {
            
            $this->beforeNormalizeUrlMap($url_map_array_data);
            $current_data = array_replace_recursive($current_data,$url_map_array_data);
            $current_data = $this->afterNormalizeUrlMap($current_data);
        }
        
        return $current_data;
    }
       
    private function beforeNormalizeUrlMap(&$url_map_array_data) {
        if (isset($url_map_array_data['init'])) {
            if (is_string(($url_map_array_data['init']))) {
                $url_map_array_data['init'] = array(self::ROOT_PATH => $url_map_array_data['init']);
            }
        }
        if (isset($url_map_array_data['before_exec'])) {
            if (is_string(($url_map_array_data['before_exec']))) {
                $url_map_array_data['before_exec'] = array(self::ROOT_PATH => $url_map_array_data['before_exec']);
            }
        }
        if (isset($url_map_array_data['exec'])) {
            if (is_string(($url_map_array_data['exec']))) {
                $url_map_array_data['exec'] = array(self::ROOT_PATH => $url_map_array_data['exec']);
            }
        }
        if (isset($url_map_array_data['after_exec'])) {
            if (is_string(($url_map_array_data['after_exec']))) {
                $url_map_array_data['after_exec'] = array(self::ROOT_PATH => $url_map_array_data['after_exec']);
            }
        }
    }
    
    private function afterNormalizeUrlMap($url_map_data) {
        $url_map_hash = new LTreeMap($url_map_data);
        
        $area_list = ['init','exec','session','input'];
        foreach ($area_list as $area) {
            if ($url_map_hash->is_set('/'.$area)) {
                $keys = $url_map_hash->keys('/'.$area);
                foreach ($keys as $key) {
                    if (LStringUtils::startsWith($key, '-')) {
                        $key_ok = substr($key, 1);
                        $url_map_hash->remove('/'.$area.'/'.$key_ok);
                        $url_map_hash->remove('/'.$area.'/'.$key);
                    }
                    if (LStringUtils::startsWith($key, '+')) {
                        $key_ok = substr($key, 1);
                        $url_map_hash->merge('/'.$area.'/'.$key_ok, $url_map_hash->mustGetOriginal('/'.$area.'/'.$key));
                        $url_map_hash->remove('/'.$area.'/'.$key);
                    }
                }
            }
        }
                
        return $url_map_hash->getRoot();
    }
    
    
}

