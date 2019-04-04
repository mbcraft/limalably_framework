<?php

class LHttpError extends LHttpResponse {
    
    private $error_code;
    
    function __construct($error_code) {
        $this->error_code = $error_code;
    }
    
    function execute() {
        http_response_code($this->error_code);
        
        $error_page_template = LConfigReader::executionMode('/error/template_by_http_code/'.$this->error_code);
        
        $template_source_factory_class_name = LConfigReader::simple('/template/source_factory_class');
        
        $factory_instance = new $template_source_factory_class_name();
        
        $template_folder = LConfigReader::simple('/template/root_folder');
        $cache_folder = LConfigReader::simple('/template/cache_folder');
        
        $file_source = $factory_instance->createFileTemplateSource($template_folder,$cache_folder);
        
        $template_path = $file_source->searchTemplate($error_page_template);
        
        if ($template_path) {
            $template = $file_source->getTemplate($template_path);
            
            $output = new LTreeMap();
            if (LWarningList::hasWarnings()) {
                LWarningList::mergeIntoTreeMap($output);
            }
            if (LErrorList::hasErrors()) {
                LErrorList::mergeIntoTreeMap($output);
            }
            
            $output_root_array = $output->getRoot();
            
            echo $template->render($output_root_array);
        }
        exit;
    }
    
}
