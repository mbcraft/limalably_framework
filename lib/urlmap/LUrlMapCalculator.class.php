<?php

class LUrlMapCalculator {
    
    static $url_maps_data = [];
        
    public static function shiftUrlmapFile($json_filename_path) {
        
        $array_data = json_decode(file_get_contents($json_filename_path), true);
        
        array_shift(self::$url_maps_data,$array_data);
        
    }
    
    public static function calculate() {
        self::$final_urlmap_data = new LHashMap();
        
        $current_data = [];
        foreach (self::$url_maps_data as $url_map_array_data) {
            $current_data = array_replace_recursive($current_data,$url_map_array_data);
            $current_data = self::normalizeUrlMap($current_data);
        }
        
        return new LHashMap($current_data);
    }
    
    private static function normalizeUrlMap($url_map_data) {
        $url_map_hash = new LHashMap($url_map_data);
    }
    
    
}

