<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LHtmlResponse extends LHttpResponse {
    
    private $my_data = null;
    
    function __construct($data) {
        $this->my_data = $data;
    }

    public function execute($format=null) {
        
        if ($format!=LFormat::HTML) throw new \Exception("Format inside response is not html.");
        
        header("Content-Type: text/html; charset=utf-8");
        header("Content-Length: ".strlen($this->my_data));
        header("Connection: close");
        
        echo $this->my_data;
        
        Lymz::finish();
    }

}
