<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LPhpFileTemplateSource implements LITemplateSource {

	private $templates_path;
	private $engine_name;

	function __construct(string $engine_name,$templates_path) {

		$this->engine_name = $engine_name;
		$this->templates_path = $templates_path;

	}

    function searchTemplate($path) {

        $f = new LFile($this->templates_path.$path);

        if ($f->exists()) return $path;

    	$extension_search_list = LConfigReader::simple('/template/'.$this->engine_name.'/extension_search_list');

    	foreach ($extension_search_list as $ext) {

    		$f = new LFile($this->templates_path.$path.$ext);

    		if ($f->exists()) return $path.$ext;
    	}

    	return false;
    }
    
    function getTemplate($path) {

    	$content = file_get_contents($this->templates_path.$path);

    	return new LPhpTemplate($content);

    }
}