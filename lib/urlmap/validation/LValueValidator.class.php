<?php

class LValueValidator {
    
    const AVAILABLE_TYPES = ['boolean','string','number','list','hashmap','list','email','url','ip','date','datetime','time'];
    const AVAILABLE_SUBTYPES = ['boolean','string','number','list','email','url','ip','date','datetime','time'];
        
    private $type;
    private $keys;
    private $values;
    private $name;
    
    function __construct($name,$params) {
        $this->name = $name;
        
        if (!isset($params['type'])) throw new \Exception();
    }
    
    function validate($name,$value,$type,$subtype) {
        if (!in_array($type, self::AVAILABLE_TYPES)) throw new \Exception("Invalid validation type : '".$type."'. Supported types are : ".var_export(self::AVAILABLE_TYPES,true));
        if ($type=='list' || $type=='hashmap' && $subtype!==null) {
            if (in_array($subtype, self::AVAILABLE_SUBTYPES)) throw new \Exception("Invalid validation subtype : '".$subtype."'. Available subtypes are : ". var_export(self::AVAILABLE_SUBTYPES, true));
        } elseif ($subtype!==null) throw new Exception ("Not needed validation subtype : '".$subtype."' for validation of type : '".$type."'.");
        
        $method_name = LStringUtils::underscoredToCamelCase("validate_".$type);
        $type_valid = $this->{$method_name}($value);
        if (!$type_valid) throw new \Exception("Invalid value for parameter '".$name."'. Value : ".var_export($value,true).". Type : ".$type);
    }
    
    private function validateHashmap($value) {
        return is_array($value);
    }
    
    private function validateList($value) {
        if (!is_array($value)) return false;
        
        $current_index = 0;
        foreach ($value as $k => $v) {
            if ($k!==$current_index) return false;
            $current_index++;
        }
        return true;
        
    }
    
    private function validateBoolean($value) {
        return filter_var($value,FILTER_VALIDATE_BOOLEAN);
    }
    
    private function validateString($value) {
        return is_string($value);
    }
    
    private function validateNumber($value) {
        return is_numeric($value);
    }
    
    private function validateEmail($value) {
        return filter_var($value,FILTER_VALIDATE_EMAIL);
    }
    
    private function validateUrl($value) {
        return filter_var($value,FILTER_VALIDATE_URL);
    }
    
    private function validateIp($value) {
        return filter_var($value,FILTER_VALIDATE_IP);
    }
    
}