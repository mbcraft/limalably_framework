<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJTemplate implements LITemplate {
    
	private $my_tree = null;

	function __construct($content) {

		$php_array = json_decode($content,true);

		$this->my_tree = new LTreeMap($php_array);

	}


    function render(array $params)
    {

    }  
    
    function getImplementationObject()
    {
    	return $this;
    }

}