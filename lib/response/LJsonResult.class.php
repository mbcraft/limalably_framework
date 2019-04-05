<?php

class LJsonResult extends LHttpResponse {

    private $my_tree;

    function __construct($tree) {
        $this->my_tree = $tree;
    }

    public function execute() {

        header("Content-Type: application/json; charset=utf-8");
        $content = LJsonUtils::encodeResult($this->my_tree);
        header("Content-Length: " . strlen($content));

        echo $content;

        exit;
    }

}
