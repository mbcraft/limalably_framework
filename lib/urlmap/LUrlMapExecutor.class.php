<?php

class LUrlMapExecutor {
    
    private $my_url_map = null;
    
    function __construct($url_map) {
        if (!$url_map instanceof LTreeMap) throw new \Exception("Url map is not valid");
        $this->my_url_map = $url_map;
    }
    
    function execute($treemap_input,$treemap_session) {
        //capture resolution
        //input parameters check
        //session parameters check
        //exec tree
        //template rendering
    }
    
}
