<?php

class LParameters {
    
    static function count() {
        if (isset($_SERVER['PARAMETERS']))
            return count($_SERVER['PARAMETERS']);
        else return 0;
    }
    
    static function getByIndex($index) {
        
        if ($index<0) throw new \Exception("Can't obtain a parameter with a negative index!");
        
        if ($index >= self::count()) throw new \Exception("Can't get parameter at index : ".$index.". Only ".self::count()." parameters are available.");
        
        if (!isset($_SERVER['PARAMETERS'][$index])) throw new \Exception("Parameter at index ".$index." is not available.");
        
        return $_SERVER['PARAMETERS'][$index];
        
    }
        
    static function all() {
        return isset($_SERVER['PARAMETERS']) ? $_SERVER['PARAMETERS'] : [];
    }
    
}
