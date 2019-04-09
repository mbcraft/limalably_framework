<?php

class LUrlMapExecutor {

    const AVAILABLE_NODES = ['conditions', '!conditions', 'load', 'init', 'input', 'session', 'before_exec', 'dynamic_exec', 'exec', 'after_exec', 'dynamic_template', 'template', 'format'];

    private $my_url_map = null;
    private $is_root = false;
    private $my_format = null;

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
        LResult::framework_debug("Executing root request : " . $route);
        $this->is_root = true;

        $parameters = isset($_SERVER['PARAMETERS']) ? $_SERVER['PARAMETERS'] : [];
        $capture = [];
        $input_tree = LInputUtils::create();
        $session_tree = LSessionUtils::create();

        try {
            $result = $this->execute($route, $parameters, $capture, $input_tree, $session_tree);

            if (!$result) {
                //a script executed correctly
                exit;
            } else {
                throw new \Exception("Unexpected state, result returned to root : " . var_export($result, true));
            }
        } catch (\LHttpResponse $response) {
            $response->execute($this->my_format);
        } catch (\Exception $ex) {
            LResult::exception($ex);
        }
    }

    public function execute($route, $parameters, $capture, $treeview_input, $treeview_session) {

        LResult::framework_debug("Start evaluating routemap for route : " . $route);

        $abs_input = $treeview_input->view('/');
        $abs_session = $treeview_session->view('/');

        $output = new LTreeMap();
        $output->set('/success', true);
        $treeview_output = $output->view('/');

        //checking for invalid nodes
        $current_keys = $this->my_url_map->keys('/');
        foreach ($current_keys as $urlmap_key) {
            if (!in_array($urlmap_key, self::AVAILABLE_NODES)) {    //ok cerca nei valori
                LErrorList::saveFromErrors('urlmap', 'Urlmap contains one one or more invalid nodes : ' . $urlmap_key);
            }
        }

        //evaluating condition
        if ($this->my_url_map->is_set('/conditions')) {
            LResult::framework_debug("Evaluating urlmap conditions ...");
            $cond = new LCondition();

            $result = $cond->evaluate('urlmap', $this->my_url_map->get('/conditions'));

            if (!$result) {
                //da valutare se usare un throw forbidden
                LErrorList::saveFromErrors('conditions', "Urlmap conditions are not verified, can't process route " . $route);
            }
        }
        //negated condition
        if ($this->my_url_map->is_set('/!conditions')) {
            LResult::framework_debug("Evaluating urlmap negative conditions ...");
            $cond = new LCondition();

            $result = $cond->evaluate('urlmap', $this->my_url_map->get('/!conditions'));

            if ($result) {
                //da valutare se usare un throw forbidden
                LErrorList::saveFromErrors('conditions', "Urlmap conditions are not verified, can't process route " . $route);
            }
        }


        //loading prepared input
        if ($this->my_url_map->is_set('/load')) {
            LResult::framework_debug("Loading data into input ...");
            try {
                $this->loadDataInTreeMap($this->my_url_map->get('/load'), $treeview_input);
            } catch (\Exception $ex) {
                LErrorList::saveFromException('load', $ex);
            }
        }


        //capture resolution
        if ($this->my_url_map->is_set('/capture')) {
            LResult::framework_debug("Evaluating capture ...");
            try {
                $capture_resolver = new LRouteCapture();
                $capture_pattern = $this->my_url_map->get('/capture');
                $capture = $capture_resolver->captureParameters($capture_pattern, $route);
            } catch (\Exception $ex) {
                LErrorList::saveFromException('capture', $ex);
            }
        } else {
            $capture = [];
        }

        //init tree

        if ($this->my_url_map->is_set('/init')) {
            LResult::framework_debug("Evaluating init section ...");
            $exec_list = $this->my_url_map->get('/init');
            foreach ($exec_list as $path => $exec_spec_list) {
                if (!is_array($exec_spec_list))
                    $exec_spec_list = array($exec_spec_list);

                $output_view = $treeview_output->view($path);
                $input_view = $treeview_input->view($path);
                $session_view = $treeview_session->view($path);

                $call_params = ['rel_output' => $output_view, 'rel_input' => $input_view, 'rel_session' => $session_view, 'input' => $abs_input, 'session' => $abs_session, 'context_path' => $path, 'capture' => $capture, 'parameters' => $parameters];

                foreach ($exec_spec_list as $call_spec) {
                    $executor = new LExecCall();
                    try {
                        $executor->execute($call_spec, $call_params);
                    } catch (\Exception $ex) {
                        LErrorList::saveFromException('init', $ex);
                    }
                }
            }
        }

        //session parameters check

        if ($this->my_url_map->is_set('/session')) {
            LResult::framework_debug("Evaluating session parameters constraints ...");
            $session_validator = new LParameterGroupValidator($treeview_session, $this->my_url_map->get('/session'));
            LErrorList::saveFromErrors('session', $session_validator->validate('session', $treeview_input, $treeview_session));
        }

        //input parameters check

        if ($this->my_url_map->is_set('/input')) {
            LResult::framework_debug("Evaluating input parameters contraints ...");
            $input_validator = new LParameterGroupValidator( $treeview_input, $this->my_url_map->get('/input'));
            LErrorList::saveFromErrors('input', $input_validator->validate('input',$treeview_input, $treeview_session));
        }

        //before exec tree

        if ($this->my_url_map->is_set('/before_exec')) {
            LResult::framework_debug("Evaluating before_exec section ...");
            $exec_list = $this->my_url_map->get('/before_exec');
            foreach ($exec_list as $path => $exec_spec_list) {
                if (!is_array($exec_spec_list))
                    $exec_spec_list = array($exec_spec_list);

                $output_view = $treeview_output->view($path);
                $input_view = $treeview_input->view($path);
                $session_view = $treeview_session->view($path);

                $call_params = ['rel_output' => $output_view, 'rel_input' => $input_view, 'rel_session' => $session_view, 'input' => $abs_input, 'session' => $abs_session, 'context_path' => $path, 'capture' => $capture, 'parameters' => $parameters];

                foreach ($exec_spec_list as $call_spec) {
                    $executor = new LExecCall();
                    try {
                        $executor->execute($call_spec, $call_params);
                    } catch (\Exception $ex) {
                        LErrorList::saveFromException('before_exec', $ex);
                    }
                }
            }
        }

        //dynamic exec

        if ($this->my_url_map->is_set('/dynamic_exec')) {
            LResult::framework_debug("Evaluating dynamic_exec section ...");
            $exec_list = $this->my_url_map->get('/dynamic_exec');
            if (!is_array($exec_list))
                $exec_list = array($exec_list);

            $call_params = ['output' => $output, 'input' => $abs_input, 'rel_input' => $treeview_input, 'session' => $abs_session, 'rel_session' => $treeview_session, 'capture' => $capture, 'parameters' => $parameters];

            $dynamic = new LDynamicCall();

            foreach ($exec_list as $call_spec) {
                try {
                    $dynamic->saveIntoExec($call_spec, $call_params, $this->my_url_map);
                } catch (\Exception $ex) {
                    LErrorList::saveFromException('dynamic_exec', $ex);
                }
            }
        }


        //exec tree

        if ($this->my_url_map->is_set('/exec')) {
            LResult::framework_debug("Evaluating exec section ...");
            $exec_list = $this->my_url_map->get('/exec');
            foreach ($exec_list as $path => $exec_spec_list) {
                if (!is_array($exec_spec_list))
                    $exec_spec_list = array($exec_spec_list);

                $output_view = $treeview_output->view($path);
                $input_view = $treeview_input->view($path);
                $session_view = $treeview_session->view($path);

                $call_params = ['rel_output' => $output_view, 'rel_input' => $input_view, 'rel_session' => $session_view, 'input' => $abs_input, 'session' => $abs_session, 'context_path' => $path, 'capture' => $capture, 'parameters' => $parameters];

                foreach ($exec_spec_list as $call_spec) {
                    $executor = new LExecCall();
                    try {
                        $executor->execute($call_spec, $call_params);
                    } catch (\Exception $ex) {
                        LErrorList::saveFromException('exec', $ex);
                    }
                }
            }
        }

        //after exec tree

        if ($this->my_url_map->is_set('/after_exec')) {
            LResult::framework_debug("Evaluating after_exec section ...");
            $exec_list = $this->my_url_map->get('/after_exec');
            foreach ($exec_list as $path => $exec_spec_list) {
                if (!is_array($exec_spec_list))
                    $exec_spec_list = array($exec_spec_list);

                $output_view = $treeview_output->view($path);
                $input_view = $treeview_input->view($path);
                $session_view = $treeview_session->view($path);

                $call_params = ['rel_output' => $output_view, 'rel_input' => $input_view, 'rel_session' => $session_view, 'input' => $abs_input, 'session' => $abs_session, 'context_path' => $path, 'capture' => $capture, 'parameters' => $parameters];

                foreach ($exec_spec_list as $call_spec) {
                    $executor = new LExecCall();
                    try {
                        $executor->execute($call_spec, $call_params);
                    } catch (\Exception $ex) {
                        LErrorList::saveFromException('after_exec', $ex);
                    }
                }
            }
        }

        //dynamic template

        if ($this->my_url_map->is_set('/dynamic_template')) {
            LResult::framework_debug("Evaluating dynamic_template ...");
            $dynamic_template_spec = $this->my_url_map->get('/dynamic_template');
            if (!is_string($dynamic_template_spec)) {
                $errors['dynamic_template'][] = "Unable to execute dynamic template call : value is not a string.";
            } else {
                $dynamic = new LDynamicCall();

                $call_params = ['output' => $output, 'input' => $abs_input, 'rel_input' => $treeview_input, 'session' => $abs_session, 'rel_session' => $treeview_session, 'capture' => $capture, 'parameters' => $parameters];
                try {
                    $dynamic->saveIntoTemplate($dynamic_template_spec, $call_params, $this->my_url_map);
                } catch (\Exception $ex) {
                    LErrorList::saveFromException('dynamic_template', $ex);
                }
            }
        }

        //template rendering

        if ($this->my_url_map->is_set('/template')) {
            LResult::framework_debug("Evaluating template ...");
            $template_path = $this->my_url_map->get('/template');

            $renderer = new LTemplateRendering($this->my_url_map, $treeview_input, $treeview_session, $capture, $parameters, $output);

            LResult::framework_debug("Searching for template : " . $template_path);

            $my_template_path = $renderer->searchTemplate($template_path);

            LResult::framework_debug("Found template at path : " . $my_template_path);

            if (!$my_template_path)
                throw new \Exception("Unable to find template : " . $template_path);

            if (!$this->my_url_map->is_set('/format')) {

                if (LStringUtils::endsWith($my_template_path, LFormat::HTML)) {
                    LResult::framework_debug("Setting response format as html.");
                    $this->my_format = LFormat::HTML;
                }

                if (LStringUtils::endsWith($my_template_path, LFormat::JSON)) {
                    LResult::framework_debug("Setting response format as json.");
                    $this->my_format = LFormat::JSON;
                }

                if (LStringUtils::endsWith($my_template_path, LFormat::XML)) {
                    LResult::framework_debug("Setting response format as xml.");
                    $this->my_format = LFormat::XML;
                }
            } else {
                $this->my_format = $this->my_url_map->get('/format');
                LResult::framework_debug("Recognized format inside urlmap : ".$this->my_format);
            }
            $result = $renderer->render($my_template_path);

            if ($result) {
                if ($this->is_root) {
                    switch ($this->my_format) {
                        case LFormat::HTML : throw new LHtmlResponse($result);
                        case LFormat::JSON : throw new LJsonResponse($result);
                        case LFormat::XML : throw new LXmlResponse($result);

                        default : throw new \Exception("Unrecognized response format ...");
                    }
                } else
                    return $result;
            }
        }

        //format resolution
        LResult::framework_debug("Evaluating response format ...");
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
            $content = LJsonUtils::encodeResult($output);
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
                return $output;
            }
        }
    }

}
