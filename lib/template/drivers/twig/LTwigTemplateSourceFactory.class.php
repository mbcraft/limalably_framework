<?php

class LTwigTemplateSourceFactory implements LITemplateSourceFactory {
    
    public function createFileTemplateSource(string $root_path,string $cache_path) {
        return new LTwigFileTemplateSource($root_path, $cache_path);
    }

    public function createStringArrayTemplateSource(array $data_map,string $cache_path) {
        return new LTwigStringArrayTemplateSource($data_map,$cache_path);
    }

    public function createTemplateFromString(string $template_source) {
        $loader = new \Twig\Loader\ArrayLoader(['template_source' => $template_source]);
        
        $params = [];
        $params['strict_variables'] = LConfigReader::executionMode('/urlmap/templates/strict_variables');
        
        $env = new \Twig\Environment($loader,$params);
        
        return new LTwigTemplate($env->load('template_source'));
    }

}
