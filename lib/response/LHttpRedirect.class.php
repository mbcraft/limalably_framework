<?php

class LHttpRedirect extends LHttpResponse {

    private $location;
    
    function __construct($location) {
        $this->location = $location;
        parent::__construct();
    }
    
    function execute($format=null) {
        
        header("Location: ".$this->location); 
        exit();
        
    }

}
