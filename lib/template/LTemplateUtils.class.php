<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTemplateUtils {

    public static function findTemplateSourceFactoryName(string $engine = null) {
        
        $engine_list = LConfigReader::simple('/template');

        if ($engine==null && count($engine_list)>1) throw new \Exception("Unable to find suitable template source factory name : ".var_export($engine_list));


        foreach ($engine_list as $engine_name => $engine_specs) {
            if ($engine == $engine_name || $engine==null) {
                return $engine_name;
            }
        }

        throw new \Exception("Unable to find suitable engine name.");
    }

    public static function findTemplateSourceFactoryInstance(string $engine = null) {
        
        $engine_name = LTemplateUtils::findTemplateSourceFactoryName($engine);

        $source_factory_class = LConfigReader::simple('/template/'.$engine_name.'/source_factory_class');

        return new $source_factory_class($engine_name);
            
    }
    
    public static function createTemplateFromString($template_source,$engine = null) {
        
        $engine_name = self::findTemplateSourceFactoryName($engine);

        $factory_instance = self::findTemplateSourceFactoryInstance($engine_name);

        return $factory_instance->createTemplateFromString($template_source);
                
    }
    
}
