<?php

class LXmlResponse extends LHttpResponse {
    
    private $my_result;
    
    function __construct($data) {
        $this->my_result = $data;
    }

    public function execute($format = null) {
        header("Content-Type: application/xml; charset=utf-8");
        header("Content-Length: " . strlen($this->my_result));
        header("Connection: close");
        
        echo $this->my_result;

        Lymlym::finish();
    }

}
