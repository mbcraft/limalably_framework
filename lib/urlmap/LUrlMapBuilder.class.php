<?php

class LUrlMapBuilder {
    
    private $my_map;
    
    public function __construct() {
        $this->my_map = new LTreeMap();
    }
    
    function setFormat($format) {
        if (!in_array($format, LResponseFormat::FORMAT_LIST))   //ok cerca nei valori
                throw new \Exception("Invalid response format.");
        
        $this->my_map->set('/format', $format);
    }
    
    function setUrlMapLink($route_link) {
        $this->my_map->set("/urlmap_link",$route_link);
    }
    
    function setExecDo($exec_do) {
        $this->my_map->set('/exec/do',$exec_do);
    }
    
    function getUrlMapData() {
        return $this->my_map;
    }
    
}