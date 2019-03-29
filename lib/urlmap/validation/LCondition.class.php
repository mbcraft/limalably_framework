<?php

class LCondition {
    
    private $condition_list;
        
    function evaluate($condition_list) {
        if ($condition_list===null) throw new \Exception("A condition list is needed to evaluate a condition");
        
        if (is_bool($condition_list)) return $condition_list;
        foreach ($condition_list as $k => $v) {
            if (is_array($v)) {
                if (!in_array($k, $v)) return false;
            } else {
                if ($k != $v) return false;
            }
        }
        return true;
    }
    
}