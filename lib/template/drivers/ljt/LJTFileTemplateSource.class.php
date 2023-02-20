<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJTFileTemplateSource implements LITemplateSource {

	private $templates_path;

	function __construct($templates_path) {

		$this->templates_path = $templates_path;

	}

    function searchTemplate($path) {

		$f = new LFile($this->templates_path.$path);

		if ($f->exists()) return $path;

    	$extension_search_list = LConfigReader::simple('/template/ljt/extension_search_list');

    	foreach ($extension_search_list as $ext) {

    		$f = new LFile($this->templates_path.$path.$ext);

    		if ($f->exists()) return $path.$ext;
    	}

    	return false;
    }

    function hasRootFolder() {
    	return true;
    }

    function getRootFolder() {
    	return $this->templates_path;
    }
    
    function getTemplate($path) {

    	$content = file_get_contents($this->templates_path.$path);

    	return new LJTemplate($content);

    }
}