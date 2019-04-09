<?php

class LUrlMapTemplateSourceFactory {
    
    public function createFileTemplateSource() {
        $template_factory_class = LConfigReader::simple('/template/source_factory_class');
                
        $factory = new $template_factory_class();
        
        $root_folder = LConfigReader::simple('/template/root_folder');
        $cache_path = LConfigReader::simple('/template/cache_folder');
        
        return $factory->createFileTemplateSource($root_folder,$cache_path);
    }

    public function createStringArrayTemplateSource(array $data_map) {
        $template_factory_class = LConfigReader::simple('/template/source_factory_class');
                
        $factory = new $template_factory_class();
        
        $cache_path = LConfigReader::simple('/template/cache_folder');
        
        return $factory->createStringArrayTemplateSource($data_map,$cache_path);
    }

    public function createTemplateFromString(string $template_source) {
        $template_factory_class = LConfigReader::simple('/template/source_factory_class');
                
        $factory = new $template_factory_class();
        
        $factory->createTemplateFromString($template_source);
    }
    
}
