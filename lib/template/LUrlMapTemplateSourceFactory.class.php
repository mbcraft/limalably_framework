<?php

class LUrlMapTemplateSourceFactory {
    
    public function createFileTemplateSource() {
        $template_factory_class = LConfigReader::simple('/urlmap/templates/source_factory_class');
                
        $factory = new $template_factory_class();
        
        $root_path = LConfigReader::simple('/urlmap/templates/root_path');
        $cache_path = LConfigReader::simple('/urlmap/templates/cache_path');
        
        return $factory->createFileTemplateSource($root_path,$cache_path);
    }

    public function createStringArrayTemplateSource(array $data_map) {
        $template_factory_class = LConfigReader::simple('/urlmap/templates/source_factory_class');
                
        $factory = new $template_factory_class();
        
        $cache_path = LConfigReader::simple('/urlmap/templates/cache_path');
        
        return $factory->createStringArrayTemplateSource($data_map,$cache_path);
    }

    public function createTemplateFromString(string $template_source) {
        $template_factory_class = LConfigReader::simple('/urlmap/templates/source_factory_class');
                
        $factory = new $template_factory_class();
        
        $factory->createTemplateFromString($template_source);
    }
    
}
