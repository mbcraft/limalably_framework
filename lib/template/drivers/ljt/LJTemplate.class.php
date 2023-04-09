<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJTemplate implements LITemplate {
    
    private $path;
    private $content;
    private $root = null;
	private $my_data = null;
    private $current_position = "";

	function __construct($path,$content) {

        $this->path = $path;
        $this->content = $content;

	}

    private function loadTemplateData() {
        if ($this->my_data) return;

        try {

          $this->my_data = json_decode($this->content,true);

          } catch (\Exception $ex) {
            throw new \Exception("Error during json decode in template ".$this->path." : ".$ex->getMessage());
        }

        if (!is_array($this->my_data)) throw new \Exception("Error in json template syntax of ".$this->path.". Check it.");

    }

    private function loadTemplateNode(array $params) {

        $final_data = array_merge($this->my_data,$params);

        $this->root = new LJTemplateRoot($final_data);
    }

    function render(array $params)
    {
        $this->loadTemplateData();

        $this->loadTemplateNode($params);

        return $this->root->render();
    }  
    
    function getImplementationObject()
    {
    	return $this;
    }

}