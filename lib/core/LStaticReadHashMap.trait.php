<?php

trait LStaticReadHashMap {
    
    static function get($param_name,$default_value = null) {
        if (isset(self::$data[$param_name])) {
            return filter_var(self::$data[$param_name]);
        }
        else {
            return $default_value;
        }
    }
    
    static function getOriginal($param_name,$default_value = null) {
        if (isset(self::$data[$param_name])) {
            return self::$data[$param_name];
        }
        else {
            return $default_value;
        }
    }
    
    static function getBoolean($param_name,$default_value = null) {
        if (isset(self::$data[$param_name])) {
            return self::$data[$param_name];
        } else {
            return $default_value;
        }
    }
    
    static function mustGet($param) {
        
    }
    
}
