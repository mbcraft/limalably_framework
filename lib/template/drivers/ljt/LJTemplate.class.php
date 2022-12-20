<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

function tag(string $original_tag_name) {
    if ($original_tag_name) {
        return LTagLib::createTagFromLibrary($original_tag_name);
    } else {
        return new LTag();
    }
}

function tagref($child_name) {
    return new LTagReference($child_name);
}

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