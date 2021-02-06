<?php

class LHttpError extends LHttpResponse {
    
    const ERROR_BAD_REQUEST = '400';
    const ERROR_UNAUTHORIZED = '401';
    const ERROR_FORBIDDEN = '403';
    const ERROR_PAGE_NOT_FOUND = '404';
    const ERROR_METHOD_NOT_ALLOWED = '405';
    const ERROR_IM_A_TEAPOT = '418';
    const ERROR_TOO_MANY_REQUESTS = '429';
    const ERROR_INTERNAL_SERVER_ERROR = '500';
    const ERROR_NOT_IMPLEMENTED = '501';
    const ERROR_SERVICE_UNAVAILABLE = '503';
    
    private $error_code;
        
    function __construct($error_code) {
        $this->error_code = $error_code;
        $this->output = new LTreeMap();
        parent::__construct("Http error ".$error_code);
    }
    
    function execute($format = null) {
        
        if ($format == null || $format == LFormat::DATA) $format = LConfigReader::simple('/format/default_error_format');
        
        //http_response_code($this->error_code);
        
        $error_templates_folder = LConfigReader::executionMode('/format/'.$format.'/error_templates_folder');
        
        $template_renderer = new LTemplateRendering($this->urlmap,$this->input,$this->session,$this->capture,$this->parameters,$this->output);
        
        $template_path = $template_renderer->searchTemplate($error_templates_folder.$this->error_code.'.'.$format);
                
        if ($template_path) {
            
            LWarningList::mergeIntoTreeMap($this->output);
            LErrorList::mergeIntoTreeMap($this->output);
            
            echo $template_renderer->render($template_path);

        } else {
            echo "HTTP error ".$this->error_code.".\n";
        }
         
        Lym::finish();
    }
    
}
