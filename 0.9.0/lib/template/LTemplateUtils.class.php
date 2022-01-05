<?php

class LTemplateUtils {
    
    static function createTemplateFromString($template_source) {
        
        $factory_class = LConfigReader::simple('/urlmap/template_source_factory_class');
        
        $factory_instance = new $factory_class();
        
        return $factory_instance->createTemplateFromString($template_source);
        
    }
    
}
