<?php

class LUrlMapCalculator {
    
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
            if (isset($url_map_array_data['exec'])) {
                if ($this->isRawExecData($url_map_array_data['exec'])) {
                    $url_map_array_data['exec'] = array('do' => $url_map_array_data['exec']);
                }
            }
            $current_data = array_replace_recursive($current_data,$url_map_array_data);
            $current_data = $this->normalizeUrlMap($current_data);
        }
        
        return $current_data;
    }
    
    private function isRawExecData($exec_array) {
        if (!is_array($exec_array)) return true;
        if (!isset($exec_array['do']) && !isset($exec_array['before']) && !isset($exec_array['after'])) return true;
        else return false;
    }
    
    private function normalizeUrlMap($url_map_data) {
        $url_map_hash = new LTreeMap($url_map_data);
        
        $area_list = ['exec','session','input','output'];
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
                        $url_map_hash->add('/'.$area.'/'.$key_ok, $url_map_hash->mustGetOriginal('/'.$area.'/'.$key));
                        $url_map_hash->remove('/'.$area.'/'.$key);
                    }
                }
            }
        }
        
        if (!$url_map_hash->is_set('/format')) {
            $template = $url_map_hash->get('/template',null);
            if ($template) {
                if (LStringUtils::startsWith($template, 'html')) {
                    $my_format = 'html';
                }
                if (LStringUtils::startsWith($template, 'json')) {
                    $my_format = 'json';
                }
                if (LStringUtils::startsWith($template, 'xml')) {
                    $my_format = 'xml';
                }
                $url_map_hash->set('/format', $my_format);
            }
            
        }
        
        return $url_map_hash->getRoot();
    }
    
    
}

