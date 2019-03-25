<?php

class LUrlMapBuilder {
    
    private $my_map;
    
    public function __construct() {
        $this->my_map = new LHashMap();
    }
    
    function setFormat($format) {
        if (!in_array($format, LResponseFormat::FORMAT_LIST))
                throw new \Exception("Invalid response format.");
        
        $this->my_map->set('/format', $format);
    }
    
    function setExecDo($exec_do) {
        $this->my_map->set('/exec/do',$exec_do);
    }
    
    function getUrlMapData() {
        return $this->my_map;
    }
    
}