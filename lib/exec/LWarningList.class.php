<?php

class LWarningList {
    
    private static $data = [];
    
    public static function clear() {
        self::$data = [];
    }
    
    public static function saveFromException(string $type,\Exception $ex) {
        self::$data[$type][] = $ex;
        self::$data['all'][] = $ex;
    }
    
    public static function saveFromWarnings(string $type,$warnings) {
        if (is_string($warnings)) $warnings = [$warnings];
                
        self::$data = array_merge_recursive(self::$data,array($type => $warnings,'all' => $warnings));
    }
    
    public static function hasWarnings() {
        return !empty(self::$data);
    }
            
    public static function mergeIntoTreeMap($treemap) {
        if (self::hasWarnings()) {
            $treemap->set('/warnings',self::$data);
        } 
    }
    
}
