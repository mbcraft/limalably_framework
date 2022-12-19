<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJTemplate implements LITemplate {
    
	private $my_data = null;

	function __construct($content) {

		$this->my_data = json_decode($content,true);

        if (!$this->my_data) throw new \Exception("Error in json template syntax. Check it.");
	}


    function render(array $params)
    {
        $root = new LJTemplateRoot($this->my_data);
        $root->parseFully();

        return "".$root;
    }  
    
    function getImplementationObject()
    {
    	return $this;
    }

}