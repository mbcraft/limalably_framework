<?php

class LCondition {
        
    const NEGATE_CONDITION_PREFIX = '!';
    
    function evaluate($node_type,$condition_list) {
        
        if ($condition_list===null) throw new \Exception("A condition list is needed to evaluate a condition for ".$node_type."!");
        
        if (is_bool($condition_list)) return $condition_list;
        
        $env_variables = LEnvironmentUtils::getReplacementsArray();
        
        foreach ($condition_list as $k => $v) {
            if (LStringUtils::startsWith($k, self::NEGATE_CONDITION_PREFIX)) {
                $my_k = substr($k,strlen(self::NEGATE_CONDITION_PREFIX));
                $negate = true;
            } else {
                $my_k = $k;
                $negate = false;
            }
            
            if (array_key_exists($my_k, $env_variables)) {
                $env_value = $env_variables[$my_k];
                if (is_array($v)) {
                    if ($negate) {
                        if (in_array($env_value, $v)) return false;  //ok cerca nei valori
                    } else {
                        if (!in_array($env_value, $v)) return false;  //ok cerca nei valori
                    }
                } else {
                    if ($negate) {
                        if ($env_value == $v) return false;
                    } else {
                        if ($env_value != $v) return false;
                    }
                }
            } else {
                throw new \Exception("Unable to evaluate ".$node_type." condition : ".$k.". Available conditions : ". array_keys($env_variables));
            }
        }
        return true;
    }
    
}