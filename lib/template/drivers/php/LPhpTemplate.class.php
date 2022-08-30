<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LPhpTemplate implements LITemplate {

	private $php_source;

	function __construct(string $php_source) {

		if (strlen($php_source)==0) throw new \Exception("Il codice sorgente del template è vuoto!");

		$this->php_source = $php_source;
	}

    function render(array $params) {

    	foreach ($params as $key => $value) {
    		$my_var = $key;
    		$$my_var = $value;
    	}
    	
    	if (isset($_SERVER['PROJECT_DIR'])) {

    		$cache_dir = $_SERVER['PROJECT_DIR'].'temp/cache/template/php/';
    	} else {
    		$cache_dir = $_SERVER['FRAMEWORK_DIR'].'lib/template/drivers/php/.cache/';
    	}

    	if (!file_exists($cache_dir)) {
    		@mkdir($cache_dir,0777);
    	}	

    	$cache_file = $cache_dir.sha1($this->php_source).'.php';

        if (!file_exists($cache_file)) {

    	   file_put_contents($cache_file,$this->php_source,LOCK_EX);
        }

    	ob_start();

    	include($cache_file);

    	$result = ob_get_contents(); 

    	ob_end_clean();

    	return $result;

    }  
    
    function getImplementationObject() {
    	return $this;
    }
}