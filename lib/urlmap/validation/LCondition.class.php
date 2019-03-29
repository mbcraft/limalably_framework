<?php

class LCondition {
    
    private $condition_list;
    
    function __construct($condition_list) {
        if ($condition_list===null) throw new \Exception("A condition list is needed to evaluate a condition");
        $this->condition_list = $condition_list;
    }
    
    function evaluate() {
        if (is_bool($this->condition_list)) return $this->condition_list;
        foreach ($this->condition_list as $k => $v) {
            if (is_array($v)) {
                if (!in_array($k, $v)) return false;
            } else {
                if ($k != $v) return false;
            }
        }
        return true;
    }
    
}