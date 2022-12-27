<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LHttpRedirect extends LHttpResponse {

    private $location;
    
    function __construct($location) {
        $this->location = $location;
    }
    
    function execute($format=null) {
        
        header("Location: ".$this->location); 
        Limalably::finish();
        
    }

}
