<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTwigTemplate implements LITemplate {
    
    private $my_template;
    
    function __construct($my_template) {
        $this->my_template = $my_template;
    }

    public function render(array $params) {
        return $this->my_template->render($params);
    }
    
    public function getImplementationObject() {
        return $this->my_template;
    }

}
