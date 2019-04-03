<?php

class LUrlMapExecutor {
    
    private $my_url_map = null;
    
    function __construct($url_map) {
        if (!$url_map instanceof LTreeMap) throw new \Exception("Url map is not valid");
        $this->my_url_map = $url_map;
    }
    

    function executeRootRequest() {
        
        $parameters = isset($_SERVER['PARAMETERS']) ? $_SERVER['PARAMETERS'] : [];
        $in = LInputUtils::create();
        
        
    }
    
    function execute($route,$parameters,$capture,$treeview_input,$treeview_session) {
        
        $output = new LTreeMap();
        $treeview_output = $output->view('/');
        
        //loading prepared input
        if ($this->my_url_map->is_set('/load')) {
            $input_loader = new LInputLoader();
            $input_loader->loadDataInTreeMap($this->my_url_map->get('/load'), $treeview_input);
        }
        
        //input parameters check
        $errors = [];
        if ($this->my_url_map->is_set('/input')) {
            $input_validator = new LParameterGroupValidator($treeview_input,$this->my_url_map->get('/input'));
            $errors['input'] = $input_validator->validate();
        }
        //session parameters check
        if ($this->my_url_map->is_set('/session')) {
            $session_validator = new LParameterGroupValidator($treeview_session,$this->my_url_map->get('/session'));
            $errors['session'] = $session_validator->validate();    
        }

        if (empty($errors)) {
        
            //capture resolution
            if ($this->my_url_map->is_set('/capture')) {
                $capture_resolver = new LRouteCapture();
                $capture_pattern = $this->my_url_map->get('/capture');
                $capture = $capture_resolver->captureParameters($capture_pattern, $route);
            } 

            //exec tree
            //
        }
        //template rendering
    }
    
}
