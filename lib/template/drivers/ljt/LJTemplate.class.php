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
    
    private $root = null;
	private $my_data = null;
    private $current_position = "LAYOUT";

	function __construct($content) {

        try {

		  $this->my_data = json_decode($content,true);

        } catch (\Exception $ex) {
            throw new \Exception("Error during json decode : ".$ex->getMessage());
        }

        if (!is_array($this->my_data)) throw new \Exception("Error in json template syntax. Check it : ".$this->my_data);

	}

    public function dumpTreeDataPositions() {
        $this->root->dumpTreeDataPositions();
    }

    function setNestedPosition($position) {
        $this->current_position = $position;
    }

    function render(array $params)
    {
        $this->root = new LJTemplateRoot($this->my_data);
        $this->root->overrideParameters($params);
        $this->root->setupStartingPosition($this->current_position);
        $this->root->parseFully();

        return "".$this->root;
    }  
    
    function getImplementationObject()
    {
    	return $this;
    }

}