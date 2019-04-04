<?php

class LTwigFileTemplateSource implements LITemplateSource {
    
    private $loader;
    private $env;
    
    function __construct($templates_path,$cache_path=null) {
        $this->loader = new \Twig\Loader\FilesystemLoader($templates_path);
        
        $params = [];
        if ($cache_path) $params['cache'] = $cache_path;
        $params['strict_variables'] = LConfigReader::executionMode('/template/strict_variables');
        $params['auto_reload'] = LConfigReader::executionMode('/template/auto_reload');
        
        $this->env = new \Twig\Environment($this->loader, $params);
    }
    
    function hasTemplate($path) {
        return $this->loader->exists($path);
    }
    
    function getTemplate($path) {
        return new LTwigTemplate($this->env->load($path));
    }
    
}
