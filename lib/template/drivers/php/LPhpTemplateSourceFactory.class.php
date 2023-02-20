<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LPhpTemplateSourceFactory implements LITemplateSourceFactory {
	
    private $root_path;

    public function supportsCache() {
        return false;
    }

    public function getEngineName() {
    	return 'php';
    }

    public function isTemplateSource(string $string_source) {
        return (strpos($string_source,'<?php')!==false || strpos($string_source,'<?')!==false);
    }
    
    public function initWithDefaults() {
        $this->root_path = LEnvironmentUtils::getBaseDir();
    }

    public function isInitialized() {
        return $this->root_path!=null;
    }
    
    function init(string $root_path) {
    	$this->root_path = $root_path;
    }
    
    function createFileTemplateSource(string $relative_folder_path,string $relative_cache_path=null) {
    	if (!$this->isInitialized()) $this->initWithDefaults ();

    	return new LPhpFileTemplateSource($this->root_path.$relative_folder_path);
    }
    
    function createStringArrayTemplateSource(array $data_map,string $relative_cache_path=null) {
    	if (!$this->isInitialized()) $this->initWithDefaults ();

    	return new LPhpStringArrayTemplateSource($data_map);
    }
    
    public function createTemplateFromString(string $template_source) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
                
        return new LPhpTemplate($template_source);
    }
}