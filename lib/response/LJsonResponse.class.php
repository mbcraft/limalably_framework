<?php

class LJsonResponse extends LHttpResponse {

    private $my_result;

    function __construct($data) {
        $this->my_result = $data;
    }

    public function execute() {

        header("Content-Type: application/json; charset=utf-8");
        header("Content-Length: " . strlen($this->my_result));
        header("Connection: close");
        
        echo $this->my_result;

        Lym::finish();
    }

}
