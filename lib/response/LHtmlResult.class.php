<?php

class LHtmlResult extends LHttpResponse {
    
    private $my_data = null;
    
    function __construct($data) {
        $this->my_data = $data;
    }

    public function execute() {
        header("Content-Type: text/html; charset=utf-8");
        header("Content-Length: ".strlen($this->my_data));
        header("Connection: close");
        
        echo $this->my_data;
        
        exit;
    }

}
