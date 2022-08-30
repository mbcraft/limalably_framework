<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTwigTemplateSourceFactory implements LITemplateSourceFactory {
    
    private $root_path = null;
    
    public function __construct(string $engine_name) {
        $this->engine_name = $engine_name;
    }

    public function getEngineName() {
        return $this->engine_name;
    }

    public function isTemplateSource(string $string_source) {
        return (strpos($string_source,'{{')!==false) || (strpos($string_source,'{%')!==false);
    }
    
    public function createFileTemplateSource(string $relative_folder_path,string $relative_cache_path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        return new LTwigFileTemplateSource($this->engine_name,$this->root_path.$relative_folder_path, $this->root_path.$relative_cache_path);
    }

    public function createStringArrayTemplateSource(array $data_map,string $relative_cache_path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        return new LTwigStringArrayTemplateSource($this->engine_name,$data_map,$this->root_path.$relative_cache_path);
    }

    public function createTemplateFromString(string $template_source) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $loader = new \Twig\Loader\ArrayLoader(['template_source' => $template_source]);
        
        $params = [];
        $params['strict_variables'] = LConfigReader::executionMode('/template/'.$this->engine_name.'/strict_variables');
        
        $env = new \Twig\Environment($loader,$params);
        
        return new LTwigTemplate($env->load('template_source'));
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

}
