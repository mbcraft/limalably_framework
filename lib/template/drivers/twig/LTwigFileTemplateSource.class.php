<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTwigFileTemplateSource implements LITemplateSource {

    private $loader;
    private $env;

    private $engine_name;

    function __construct(string $engine_name,$templates_path, $cache_path = null) {
        
        $this->engine_name = $engine_name;

        $this->loader = new \Twig\Loader\FilesystemLoader($templates_path);

        $params = [];
        if ($cache_path)
            $params['cache'] = $cache_path;
        $params['strict_variables'] = LConfigReader::executionMode('/template/'.$this->engine_name.'/strict_variables');
        $params['auto_reload'] = LConfigReader::executionMode('/template/'.$this->engine_name.'/auto_reload');
        $params['autoescape'] = LConfigReader::simple('/template/'.$this->engine_name.'/autoescape');
        
        $this->env = new \Twig\Environment($this->loader, $params);
    }

    function searchTemplate($path) {
        $extension_search_list = LConfigReader::simple('/template/'.$this->engine_name.'/extension_search_list');

        if ($this->loader->exists($path))
            return $path;

        foreach ($extension_search_list as $extension) {
            if ($this->loader->exists($path . $extension))
                return $path . $extension;
        }

        return false;
    }

    function getTemplate($path) {

        return new LTwigTemplate($this->env->load($path));
    }

}
