<?php

class LHttpError extends LHttpResponse {
    
    private $error_code;
    
    function __construct($error_code) {
        $this->error_code = $error_code;
    }
    
    function execute($format = null) {
        
        if ($format == null) throw new \Exception("A format for response is needed to render HTTP error code.");
        
        http_response_code($this->error_code);
        
        $error_templates_folder = LConfigReader::executionMode('/format/'.$format.'/error_templates_folder'.$this->error_code);
        
        $template_source_factory_class_name = LConfigReader::simple('/template/source_factory_class');
        
        $factory_instance = new $template_source_factory_class_name();
        
        $cache_folder = LConfigReader::simple('/template/cache_folder');
        
        $file_source = $factory_instance->createFileTemplateSource($error_templates_folder,$cache_folder);
        
        $template_path = $file_source->searchTemplate($this->error_code);
        
        if ($template_path) {
            $template = $file_source->getTemplate($template_path);
            
            $output = new LTreeMap();
           
            LWarningList::mergeIntoTreeMap($output);
            LErrorList::mergeIntoTreeMap($output);
            
            $output_root_array = $output->getRoot();
            
            echo $template->render($output_root_array);
        } else {
            echo "HTTP error ".$this->error_code.".";
        }
        Lym::finish();
    }
    
}
