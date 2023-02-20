<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LPhpStringArrayTemplateSource implements LITemplateSource {
	
	private $data_map;

	function __construct($data_map) {

		$this->data_map = $data_map;

	}

    function hasRootFolder() {
        return false;
    }

    function getRootFolder() {
        return null;
    }

    function searchTemplate($path) {
    	if (isset($this->data_map[$path])) return $path;
    	else return false;
    }
    
    function getTemplate($path) {

    	$content = $this->data_map[$path];

    	return new LPhpTemplate($content);

    }
}