<?php

class LTwigStringArrayTemplateSource implements LITemplateSource {
    
    private $loader;
    private $env;
    
    function __construct($data_map,$cache_path) {
        $this->loader = new \Twig\Loader\ArrayLoader($data_map);
        
        $params = [];
        if ($cache_path) $params['cache'] = $cache_path;
        $params['strict_variables'] = LConfigReader::executionMode('/template/strict_variables');
        $params['auto_reload'] = LConfigReader::executionMode('/template/auto_reload');
        
        $this->env = new \Twig\Environment($this->loader,$params);
    }
    
    public function getTemplate($path) {
        return new LTwigTemplate($this->env->load($path));
    }

    public function hasTemplate($path) {
        return $this->loader->exists($path);
    }

}