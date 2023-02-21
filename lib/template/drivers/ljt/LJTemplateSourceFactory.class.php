<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LJTemplateSourceFactory implements LITemplateSourceFactory {

    private $root_path;

    public function supportsCache() {
        return false;
    }

    public function getEngineName() {
    	return 'ljt';
    }

    public function isTemplateSource(string $string_source) {
        return (strpos(trim($string_source),'{')===0 || strrpos(trim($string_source),'}')===strlen($string_source)-1);
    }
    
    public function init(string $root_path) {
        $this->root_path = $root_path;
    }

    public function initWithDefaults() {
        $this->root_path = LEnvironmentUtils::getBaseDir();
    }

    public function isInitialized() {
        return $this->root_path!=null;
    }
    
    function createFileTemplateSource(string $relative_folder_path,string $relative_cache_path=null) {
    	if (!$this->isInitialized()) $this->initWithDefaults ();

    	return new LJTFileTemplateSource($this->root_path.$relative_folder_path);
    }
    
    function createStringArrayTemplateSource(array $data_map,string $relative_cache_path=null) {
    	if (!$this->isInitialized()) $this->initWithDefaults ();

    	return new LJTStringArrayTemplateSource($data_map);
    }
    
    public function createTemplateFromString(string $template_source) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
                
        return new LJTemplate($template_source);
    }

}