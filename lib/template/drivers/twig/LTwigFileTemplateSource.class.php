<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTwigFileTemplateSource implements LITemplateSource {

    private $loader;
    private $env;

    private $templates_folder;

    function __construct($templates_path, $cache_path = null) {

        $this->templates_path = $templates_path;

        $this->loader = new \Twig\Loader\FilesystemLoader($templates_path);

        $params = [];
        if ($cache_path)
            $params['cache'] = $cache_path;
        $params['strict_variables'] = LConfigReader::executionMode('/template/twig/strict_variables');
        $params['auto_reload'] = LConfigReader::executionMode('/template/twig/auto_reload');
        $params['autoescape'] = LConfigReader::simple('/template/twig/autoescape');
        
        $this->env = new \Twig\Environment($this->loader, $params);
    }

    function searchTemplate($path) {

        if ($this->loader->exists($path)) return $path;

        $extension_search_list = LConfigReader::simple('/template/twig/extension_search_list');

        foreach ($extension_search_list as $extension) {
            if ($this->loader->exists($path . $extension))
                return $path . $extension;
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

        return new LTwigTemplate($this->env->load($path));
    }

}
