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
        $output->set('/success',true);
        $treeview_output = $output->view('/');
        $errors = [];
        //loading prepared input
        if ($this->my_url_map->is_set('/load')) {
            try {
                $input_loader = new LInputLoader();
                $input_loader->loadDataInTreeMap($this->my_url_map->get('/load'), $treeview_input);
            } catch (\Exception $ex) {
                $errors['load'][] = $ex->getMessage();
            }
        }
        
        //input parameters check
        if (empty($errors)) {
            if ($this->my_url_map->is_set('/input')) {
                $input_validator = new LParameterGroupValidator($treeview_input,$this->my_url_map->get('/input'));
                $errors['input'] = $input_validator->validate();
            }
        }
        //session parameters check
        if (empty($errors)) {
            if ($this->my_url_map->is_set('/session')) {
                $session_validator = new LParameterGroupValidator($treeview_session,$this->my_url_map->get('/session'));
                $errors['session'] = $session_validator->validate();    
            }
        }
        
        if (empty($errors)) {
        
            //capture resolution
            if ($this->my_url_map->is_set('/capture')) {
                try {
                    $capture_resolver = new LRouteCapture();
                    $capture_pattern = $this->my_url_map->get('/capture');
                    $capture = $capture_resolver->captureParameters($capture_pattern, $route);
                } catch (\Exception $ex) {
                    $errors['capture'] = [$ex->getMessage()];
                }
            } else {
                $capture = [];
            }
        }
        
        //dynamic exec
        if (empty($errors)) {
            if ($this->my_url_map->is_set('/dynamic_exec')) {
                $exec_list = $this->my_url_map->get('/dynamic_exec');
                if (!is_array($exec_list)) $exec_list = array($exec_list);
                
                $call_params = ['output' => $output,'input' => $treeview_input,'session' => $treeview_session,'capture' => $capture,'parameters' => $parameters];
                
                $dynamic = new LDynamicCall();
                    
                foreach ($exec_list as $call_spec) {
                    try {
                        $dynamic->saveIntoExec($call_spec, $call_params, $this->my_url_map);
                    } catch (\Exception $ex) {
                        $errors['dynamic_exec'][] = $ex->getMessage();
                    }
                }
            }
        }
        
        //exec tree
        if (empty($errors)) {
            if ($this->my_url_map->is_set('/exec')) {
                $exec_list = $this->my_url_map->get('/exec');
                foreach ($exec_list as $path => $exec_spec_list) {
                    if (!is_array($exec_spec_list)) $exec_spec_list = array($exec_spec_list);
                    
                    $output_view = $treeview_output->view($path);
                    $input_view = $treeview_input->view($path);
                    $session_view = $treeview_session->view($path);
                    
                    $call_params = ['output' => $output_view,'input' => $input_view,'session' => $session_view,'context_path' => $path,'capture' => $capture,'parameters' => $parameters];
                    
                    foreach ($exec_spec_list as $call_spec) {
                        $executor = new LExecCall();
                        try {
                            $executor->execute($call_spec, $call_params);
                        } catch (\Exception $ex) {
                            $errors['exec'][] = $ex->getMessage();
                        }
                    }
                }
            }
        }
        
        //dynamic template
        if (empty($errors)) {
            if ($this->my_url_map->is_set('/dynamic_template')) {
                $dynamic_template_spec = $this->my_url_map->get('/dynamic_template');
                if (!is_string($dynamic_template_spec)) {
                    $errors['dynamic_template'][] = "Unable to execute dynamic template call : value is not a string.";
                } else {
                    $dynamic = new LDynamicCall();
                    
                    $call_params = ['output' => $output,'input' => $treeview_input,'session' => $treeview_session,'capture' => $capture,'parameters' => $parameters];
                    try {                    
                        $dynamic->saveIntoTemplate($dynamic_template_spec, $call_params, $this->my_url_map);
                    } catch (\Exception $ex) {
                        $errors['dynamic_template'][] = $ex->getMessage();
                    }
                }
            }
        }
        
        //template rendering
        if (empty($errors)) {
            if ($this->my_url_map->is_set('/template')) {
                $template_path = $this->my_url_map->get('/template');
                
                $template_factory = new LUrlMapTemplateSourceFactory();
                
                $template_source = $template_factory->createFileTemplateSource();
                
                if (!$template_source->hasTemplate($template_path)) {
                    $errors['template'] = ['Unable to file template at path : '.$template_path];
                } else {
                    $template = $template_source->getTemplate($template_path);
                    
                    //inserire fra le variabili : urlmap, input, session, capture, i18n, parameters - con eventuale prefisso di path tipo 'meta'
                    $output->set('urlmap',$this->my_url_map->get('.'));
                    $output->set('input',$treeview_input->get('.'));
                    $output->set('session',$treeview_session->get('.'));
                    $output->set('parameters',$parameters);
                    $output->set('capture',$capture);
                    
                    //TODO : manca i18n
                    try {
                        $rendered_content = $template->render($output->getRoot());
                    } catch (\Exception $ex) {
                        $errors['template'][] = $ex->getMessage();
                    }
                    return $rendered_content;
                }
            }
        }
    }
    
}
