<?php

class LTwigFileTemplateSource implements LITemplateSource {

    private $loader;
    private $env;

    function __construct($templates_path, $cache_path = null) {
        $this->loader = new \Twig\Loader\FilesystemLoader($templates_path);

        $params = [];
        if ($cache_path)
            $params['cache'] = $cache_path;
        $params['strict_variables'] = LConfigReader::executionMode('/template/strict_variables');
        $params['auto_reload'] = LConfigReader::executionMode('/template/auto_reload');
        $params['autoescape'] = LConfigReader::simple('/template/autoescape');

        var_dump($params);
        var_dump(LConfig::get('/defaults/execution_mode/maintenance/template/auto_reload'));
        
        $this->env = new \Twig\Environment($this->loader, $params);
    }

    function searchTemplate($path) {
        $extension_search_list = LConfigReader::simple('/template/extension_search_list');

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
