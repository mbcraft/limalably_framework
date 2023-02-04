<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LUrlMapExecutor {

    const AVAILABLE_NODES = ['real_url','imports','extends','conditions', '!conditions', 'capture' ,'load', 'init', 'input', 'session', 'before_exec', 'dynamic_exec', 'exec', 'after_exec', 'dynamic_template', 'template', 'format','http_error','http_redirect'];

    private $my_url_map = null;
    private $is_root = false;
    private $my_format = null;
    private $capture = null;
    private $output = null;

    function __construct($url_map) {
        if (!$url_map instanceof LTreeMap)
            throw new \Exception("Url map is not valid");
        $this->my_url_map = $url_map;
    }

    private function loadDataInTreeMap($load_node, $treemap) {
        foreach ($load_node as $key => $value) {

            $treemap->set($key, $value);
        }
    }

    function executeRootRequest($route) {
        LResult::trace("Executing root request : " . $route);
        $this->is_root = true;
        
        $parameters = LParameters::all();
        $this->capture = [];
        $input_tree = LInputUtils::create();
        $session_tree = LSessionUtils::create();

        try {
            $result = $this->execute($route, $parameters, $this->capture, $input_tree, $session_tree);

            if (!$result) {
                //a script executed correctly
                LResult::error_message("No result was returned by urlmap!");
                
                Limalably::finish(1);
            } else {
                throw new \Exception("Unexpected state, result returned to root : " . var_export($result, true));
            }
        } catch (\LHttpResponse $response) {
            $response->setup($this->my_url_map,$input_tree,$session_tree,$this->capture,$parameters,$this->output);

            $response->execute($this->my_format);

        } catch (\Exception $ex) {
            LResult::exception($ex);
        }
    }

    public function execute($route, $parameters, $capture, $treeview_input, $treeview_session) {

        $this->capture = $capture;
        
        LResult::trace("Start evaluating routemap for route : " . $route);

        $abs_input = $treeview_input->view('/');
        $abs_session = $treeview_session->view('/');

        $this->output = new LTreeMap();
        $treeview_output = $this->output->view('/');
        $flashes = LFlash::getAllMessages();

        //checking for invalid nodes
        $current_keys = $this->my_url_map->keys('/');
        foreach ($current_keys as $urlmap_key) {
            if (!in_array($urlmap_key, self::AVAILABLE_NODES)) {    //ok cerca nei valori
                LErrorList::saveFromErrors('urlmap', 'Urlmap contains one or more invalid nodes : ' . $urlmap_key);
            }
        }

        //evaluating condition
        if ($this->my_url_map->is_set('/conditions')) {
            LResult::trace("Evaluating urlmap conditions ...");
            $cond = new LCondition();

            $result = $cond->evaluate('urlmap', $this->my_url_map->get('/conditions'));

            if (!$result) {
                //da valutare se usare un throw forbidden
                LErrorList::saveFromErrors('conditions', "Urlmap conditions are not verified, can't process route " . $route);
            }
        }
        //negated condition
        if ($this->my_url_map->is_set('/!conditions')) {
            LResult::trace("Evaluating urlmap negative conditions ...");
            $cond = new LCondition();

            $result = $cond->evaluate('urlmap', $this->my_url_map->get('/!conditions'));

            if ($result) {
                //da valutare se usare un throw forbidden
                LErrorList::saveFromErrors('conditions', "Urlmap conditions are not verified, can't process route " . $route);
            }
        }


        //loading prepared input
        if ($this->my_url_map->is_set('/load')) {
            LResult::trace("Loading data into input ...");
            try {
                $this->loadDataInTreeMap($this->my_url_map->get('/load'), $treeview_input);
            } catch (\Exception $ex) {
                LErrorList::saveFromException('load', $ex);
            }
        }


        //capture resolution
        if ($this->my_url_map->is_set('/capture')) {
            LResult::trace("Evaluating capture ...");
            try {
                $capture_resolver = new LRouteCapture();
                $capture_pattern = $this->my_url_map->get('/capture');
                $this->capture = $capture_resolver->captureParameters($capture_pattern, $route);
                $_SERVER['CAPTURE'] = $this->capture;
            } catch (\Exception $ex) {
                LErrorList::saveFromException('capture', $ex);
            }
        } else {
            $this->capture = [];
        }

        //init tree

        if ($this->my_url_map->is_set('/init')) {
            LResult::trace("Evaluating init section ...");
            $exec_list = $this->my_url_map->get('/init');
            foreach ($exec_list as $path => $exec_spec_list) {
                if (!is_array($exec_spec_list)) {
                    $exec_spec_list = array($exec_spec_list);
                    $merge = false;
                } else {
                    $merge = true;
                }

                $output_view = $treeview_output->view($path);
                $input_view = $treeview_input->view($path);
                $session_view = $treeview_session->view($path);

                $call_params = ['rel_output' => $output_view, 'output' => $this->output, 'rel_input' => $input_view, 'rel_session' => $session_view, 'input' => $abs_input, 'session' => $abs_session, 'context_path' => $path, 'capture' => $this->capture, 'parameters' => $parameters,'flash' => $flashes];

                foreach ($exec_spec_list as $call_spec) {
                    $executor = new LExecCall();
                    try {
                        $executor->execute($call_spec, $call_params,$merge);
                    } catch (\Exception $ex) {
                        LErrorList::saveFromException('init', $ex);
                    }
                }
            }
        }

        //session parameters check

        if ($this->my_url_map->is_set('/session')) {
            LResult::trace("Evaluating session parameters constraints ...");
            $session_validator = new LParameterGroupValidator($treeview_session, $this->my_url_map->get('/session'));
            LErrorList::saveFromErrors('session', $session_validator->validate('session', $treeview_input, $treeview_session));
        }

        //input parameters check

        if ($this->my_url_map->is_set('/input')) {
            LResult::trace("Evaluating input parameters contraints ...");
            $input_validator = new LParameterGroupValidator( $treeview_input, $this->my_url_map->get('/input'));
            LErrorList::saveFromErrors('input', $input_validator->validate('input',$treeview_input, $treeview_session));
        }

        //loading flash from session

        LFlash::load_from_session();

        //before exec tree

        if ($this->my_url_map->is_set('/before_exec')) {
            LResult::trace("Evaluating before_exec section ...");
            $exec_list = $this->my_url_map->get('/before_exec');
            foreach ($exec_list as $path => $exec_spec_list) {
                if (!is_array($exec_spec_list)) {
                    $exec_spec_list = array($exec_spec_list);
                    $merge = false;
                } else {
                    $merge = true;
                }

                $output_view = $treeview_output->view($path);
                $input_view = $treeview_input->view($path);
                $session_view = $treeview_session->view($path);

                $call_params = ['rel_output' => $output_view, 'output' => $this->output, 'rel_input' => $input_view, 'rel_session' => $session_view, 'input' => $abs_input, 'session' => $abs_session, 'context_path' => $path, 'capture' => $this->capture, 'parameters' => $parameters,'flash' => $flashes];

                foreach ($exec_spec_list as $call_spec) {
                    $executor = new LExecCall();
                    try {
                        $executor->execute($call_spec, $call_params,$merge);
                    } catch (\Exception $ex) {
                        LErrorList::saveFromException('before_exec', $ex);
                    }
                }
            }
        }

        //dynamic exec

        if ($this->my_url_map->is_set('/dynamic_exec')) {
            LResult::trace("Evaluating dynamic_exec section ...");
            $exec_list = $this->my_url_map->get('/dynamic_exec');
            if (!is_array($exec_list)) {
                $exec_list = array($exec_list);
                $merge = false;
            } else {
                $merge = true;
            }

            $call_params = ['output' => $this->output, 'input' => $abs_input, 'rel_input' => $treeview_input, 'session' => $abs_session, 'rel_session' => $treeview_session, 'capture' => $this->capture, 'parameters' => $parameters, 'flash' => $flashes];

            $dynamic = new LDynamicCall();

            foreach ($exec_list as $call_spec) {
                try {
                    $dynamic->saveIntoExec($call_spec, $call_params, $this->my_url_map, $merge);
                } catch (\Exception $ex) {
                    LErrorList::saveFromException('dynamic_exec', $ex);
                }
            }
        }


        //exec tree

        if ($this->my_url_map->is_set('/exec')) {
            LResult::trace("Evaluating exec section ...");
            $exec_list = $this->my_url_map->get('/exec');
            foreach ($exec_list as $path => $exec_spec_list) {
                if (!is_array($exec_spec_list)) {
                    $exec_spec_list = array($exec_spec_list);
                    $merge = false;
                } else {
                    $merge = true;
                }
                $output_view = $treeview_output->view($path);
                $input_view = $treeview_input->view($path);
                $session_view = $treeview_session->view($path);

                $call_params = ['rel_output' => $output_view, 'output' => $this->output, 'rel_input' => $input_view, 'rel_session' => $session_view, 'input' => $abs_input, 'session' => $abs_session, 'context_path' => $path, 'capture' => $this->capture, 'parameters' => $parameters, 'flash' => $flashes];

                foreach ($exec_spec_list as $call_spec) {
                    $executor = new LExecCall();
                    try {
                        $executor->execute($call_spec, $call_params,$merge);
                    } catch (\Exception $ex) {
                        LErrorList::saveFromException('exec', $ex);
                    }
                }
            }
        }

        //after exec tree

        if ($this->my_url_map->is_set('/after_exec')) {
            LResult::trace("Evaluating after_exec section ...");
            $exec_list = $this->my_url_map->get('/after_exec');
            foreach ($exec_list as $path => $exec_spec_list) {
                if (!is_array($exec_spec_list)) {
                    $exec_spec_list = array($exec_spec_list);
                    $merge = false;
                } else {
                    $merge = true;
                }
                $output_view = $treeview_output->view($path);
                $input_view = $treeview_input->view($path);
                $session_view = $treeview_session->view($path);

                $call_params = ['rel_output' => $output_view, 'output' => $this->output ,'rel_input' => $input_view, 'rel_session' => $session_view, 'input' => $abs_input, 'session' => $abs_session, 'context_path' => $path, 'capture' => $this->capture, 'parameters' => $parameters, 'flash' => $flashes];

                foreach ($exec_spec_list as $call_spec) {
                    $executor = new LExecCall();
                    try {
                        $executor->execute($call_spec, $call_params, $merge);
                    } catch (\Exception $ex) {
                        LErrorList::saveFromException('after_exec', $ex);
                    }
                }
            }
        }

        //dynamic template

        if ($this->my_url_map->is_set('/dynamic_template')) {
            LResult::trace("Evaluating dynamic_template ...");
            $dynamic_template_spec = $this->my_url_map->get('/dynamic_template');
            if (!is_string($dynamic_template_spec)) {
                LErrorList::saveFromErrors('dynamic_template',"Unable to execute dynamic template call : value is not a string.");
            } else {
                $dynamic = new LDynamicCall();

                $call_params = ['output' => $this->output, 'input' => $abs_input, 'rel_input' => $treeview_input, 'session' => $abs_session, 'rel_session' => $treeview_session, 'capture' => $this->capture, 'parameters' => $parameters, 'flash' => $flashes];
                try {
                    $dynamic->saveIntoTemplate($dynamic_template_spec, $call_params, $this->my_url_map);
                } catch (\Exception $ex) {
                    LErrorList::saveFromException('dynamic_template', $ex);
                }
            }
        }
        
        //handling http errors and http redirect
        
        if ($this->my_url_map->is_set('/http_redirect')) {
            
            throw new LHttpRedirect($this->my_url_map->get('/http_redirect'));
            
        }
        
        if ($this->my_url_map->is_set('/http_error')) {
            throw new LHttpError($this->my_url_map->get('/http_error'));
        }
        
        //default format setup
        if (LConfigReader::has('/format/default_response_format')) {
            $this->my_format = LConfigReader::simple('/format/default_response_format');
        }

        //template rendering

        if ($this->my_url_map->is_set('/template/name')) {
            LResult::trace("Evaluating template ...");
            $template_path = $this->my_url_map->get('/template/name');

            $par_setup = new LParametersSetup($this->my_url_map, $treeview_input, $treeview_session, $this->capture, $parameters, $this->output);

            $par_setup->findEngineName();
            $par_setup->setupParameters();

            $renderer = new LTemplateRendering();
            $renderer->setupTemplateSource($par_setup->getEngineName());

            LResult::trace("Searching for template : " . $template_path);

            $my_template_path = $renderer->searchTemplate($template_path);

            LResult::trace("Found template at path : " . $my_template_path);

            if (!$my_template_path)
                throw new \Exception("Unable to find template : " . $template_path);

            if (!$this->my_url_map->is_set('/format')) {

                if (LStringUtils::endsWith($my_template_path, ".twig")) {
                    LResult::trace("Setting response format as html.");
                    $this->my_format = LFormat::HTML;
                }

                if (LStringUtils::endsWith($my_template_path, ".php")) {
                    LResult::trace("Setting response format as html.");
                    $this->my_format = LFormat::HTML;
                }

                if (LStringUtils::endsWith($my_template_path, LFormat::HTML)) {
                    LResult::trace("Setting response format as html.");
                    $this->my_format = LFormat::HTML;
                }

                if (LStringUtils::endsWith($my_template_path, LFormat::JSON)) {
                    LResult::trace("Setting response format as html.");
                    $this->my_format = LFormat::HTML;
                }

                if (LStringUtils::endsWith($my_template_path, LFormat::LJT)) {
                    LResult::trace("Setting response format as html.");
                    $this->my_format = LFormat::HTML;
                }

                if (LStringUtils::endsWith($my_template_path, LFormat::XML)) {
                    LResult::trace("Setting response format as xml.");
                    $this->my_format = LFormat::XML;
                }
            } else {
                $this->my_format = $this->my_url_map->get('/format');

                LResult::trace("Recognized format inside urlmap : ".$this->my_format);
            }
            LResult::trace("Rendering template ...");
            
            $result = $renderer->render($my_template_path,$par_setup->getAllParameters());
            
            LResult::trace("Template rendered correctly.");
            if ($result) {
                if ($this->is_root) {
                    switch ($this->my_format) {
                        case LFormat::HTML : throw new LHtmlResponse($result);
                        case LFormat::JSON : throw new LJsonResponse($result);
                        case LFormat::XML : throw new LXmlResponse($result);

                        default : throw new \Exception("Unrecognized response format ... : ".$this->my_format);
                    }
                } else
                    return $result;
            }
        }

        //format resolution
        LResult::trace("Evaluating response format ...");
        if ($this->my_url_map->is_set('/format')) {
            $this->my_format = $this->my_url_map->get('/format');
        } else {

            if ($this->is_root) {
                $this->my_format = LFormat::JSON;
            } else {
                $this->my_format = LFormat::DATA;
            }
        }

        if ($this->my_format == LFormat::JSON) {
            $content = LJsonUtils::encodeResult($this->output);

            if ($this->is_root) {
                throw new LJsonResponse($content);
            } else {
                return $content;
            }
        }

        if ($this->my_format == LFormat::XML) {
            throw new \Exception("Xml is not yet supported.");
        }

        if ($this->my_format == LFormat::DATA) {
            if ($this->is_root) {
                return null;
            } else {
                return $this->output;
            }
        }
    }

}
