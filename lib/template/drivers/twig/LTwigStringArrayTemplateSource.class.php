<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTwigStringArrayTemplateSource implements LITemplateSource {
    
    private $loader;
    private $env;
    
    function __construct(string $engine_name,$data_map,$cache_path) {
        $this->loader = new \Twig\Loader\ArrayLoader($data_map);
        
        $params = [];
        if ($cache_path) $params['cache'] = $cache_path;
        $params['strict_variables'] = LConfigReader::executionMode('/template/'.$engine_name.'/strict_variables');
        $params['auto_reload'] = LConfigReader::executionMode('/template/'.$engine_name.'/auto_reload');
        
        $this->env = new \Twig\Environment($this->loader,$params);
    }
    
    public function getTemplate($path) {
        return new LTwigTemplate($this->env->load($path));
    }

    public function searchTemplate($path) {
        if ($this->loader->exists($path)) return $path;
        else return false;
    }

}