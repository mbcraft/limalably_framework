<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LUrlMapTemplateSourceFactory {
    
    public function createFileTemplateSource($engine = null) {

        $engine_name = LTemplateUtils::findTemplateSourceFactoryName($engine);

        $factory = LTemplateUtils::findTemplateSourceFactoryInstance($engine_name);
        
        $root_folder = LConfigReader::simple('/template/'.$engine_name.'/root_folder');
        $cache_path = LConfigReader::simple('/template/'.$engine_name.'/cache_folder');
        
        return $factory->createFileTemplateSource($root_folder,$cache_path);
    }

    public function createStringArrayTemplateSource(array $data_map,string $engine = null) {
        
        $engine_name = LTemplateUtils::findTemplateSourceFactoryName($engine);

        $factory = LTemplateUtils::findTemplateSourceFactoryInstance($engine_name);
        
        $cache_path = LConfigReader::simple('/template/'.$engine_name.'/cache_folder');
        
        return $factory->createStringArrayTemplateSource($data_map,$cache_path);
    }

    public function createTemplateFromString(string $template_source,string $engine = null) {
            
        $engine_name = LTemplateUtils::findTemplateSourceFactoryName($engine);

        $factory = LTemplateUtils::findTemplateSourceFactoryInstance($engine_name);
        
        $factory->createTemplateFromString($template_source);
    }
    
}
