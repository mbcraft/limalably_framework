<?php

class LUrlMapExecutor {
    
    private $my_url_map = null;
    
    function __construct($url_map) {
        if (!$url_map instanceof LTreeMap) throw new \Exception("Url map is not valid");
        $this->my_url_map = $url_map;
    }
    
    function execute($input) {
        //import parametri
        //input parameters check
        //session parameters check
        //output composition
        //exec before do after
        //template rendering
    }
    
}
