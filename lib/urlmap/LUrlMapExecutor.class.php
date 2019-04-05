<?php

class LUrlMapExecutor {

    const AVAILABLE_NODES = ['conditions', '!conditions', 'load', 'input', 'session', 'dynamic_exec', 'exec', 'dynamic_template', 'template', 'format'];

    private $my_url_map = null;

    function __construct($url_map) {
        if (!$url_map instanceof LTreeMap)
            throw new \Exception("Url map is not valid");
        $this->my_url_map = $url_map;
    }

    function executeRootRequest($route) {

        $parameters = isset($_SERVER['PARAMETERS']) ? $_SERVER['PARAMETERS'] : [];
        $capture = [];
        $input_tree = LInputUtils::create();
        $session_tree = LSessionUtils::create();

        $result = $this->execute($route, $parameters, $capture, $input_tree, $session_tree);
        
        if ($result instanceof LTreeMap || $result instanceof LTreeMapView) {
            echo $this->jsonEncodeResult($result);
        } else {
            echo $result;
        }
    }

    function jsonEncodeResult($output) {
        $encode_options_list = LConfigReader::simple('/format/json/encode_options');
        $encode_options = 0;
        foreach ($encode_options_list as $enc_opt) {
            try {
                $encode_options |= eval('return JSON_' . $enc_opt . ';');
            } catch (\Exception $ex) {
                LErrorList::saveFromErrors('format', 'Invalid json encode format : JSON_' . $enc_opt . ' does not evaluate to an integer value.');
            }
        }

        LWarningList::mergeIntoTreeMap($output);
        LErrorList::mergeIntoTreeMap($output);
        
        $output_data = $output->getRoot();

        try {
            $content = json_encode($output_data, $encode_options);
            LWarningList::clear();
            LErrorList::clear();
            return $content;
        } catch (\Exception $ex) {
            LErrorList::saveFromException('format', $ex);
        }
    }

    function execute($route, $parameters, $capture, $treeview_input, $treeview_session) {

        $output = new LTreeMap();
        $output->set('/success', true);
        $treeview_output = $output->view('/');

        //checking for invalid nodes
        $current_keys = $this->my_url_map->keys('/');
        foreach ($current_keys as $urlmap_key) {
            if (!in_array($urlmap_key, self::AVAILABLE_NODES)) {
                LErrorList::saveFromErrors('urlmap', 'Urlmap contains one one or more invalid nodes : ' . $urlmap_key);
            }
        }

        //evaluating condition
        if ($this->my_url_map->is_set('/conditions')) {
            $cond = new LCondition();

            $result = $cond->evaluate('urlmap', $this->my_url_map->get('/condition'));

            if (!$result) {
                //da valutare se usare un throw forbidden
                LErrorList::saveFromErrors('conditions', "Urlmap conditions are not verified, can't process route " . $route);
            }
        }
        //negated condition
        if ($this->my_url_map->is_set('/!conditions')) {
            $cond = new LCondition();

            $result = $cond->evaluate('urlmap', $this->my_url_map->get('/condition'));

            if ($result) {
                //da valutare se usare un throw forbidden
                LErrorList::saveFromErrors('conditions', "Urlmap conditions are not verified, can't process route " . $route);
            }
        }

        //loading prepared input
        if ($this->my_url_map->is_set('/load')) {
            try {
                $input_loader = new LInputLoader();
                $input_loader->loadDataInTreeMap($this->my_url_map->get('/load'), $treeview_input);
            } catch (\Exception $ex) {
                LErrorList::saveFromException('load', $ex);
            }
        }

        //input parameters check

        if ($this->my_url_map->is_set('/input')) {
            $input_validator = new LParameterGroupValidator('input', $treeview_input, $this->my_url_map->get('/input'));
            LErrorList::saveFromErrors('input', $input_validator->validate($treeview_input, $treeview_session));
        }

        //session parameters check

        if ($this->my_url_map->is_set('/session')) {
            $session_validator = new LParameterGroupValidator('session', $treeview_session, $this->my_url_map->get('/session'));
            LErrorList::saveFromErrors('session', $session_validator->validate($treeview_input, $treeview_session));
        }




        //capture resolution
        if ($this->my_url_map->is_set('/capture')) {
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


        //dynamic exec

        if ($this->my_url_map->is_set('/dynamic_exec')) {
            $exec_list = $this->my_url_map->get('/dynamic_exec');
            if (!is_array($exec_list))
                $exec_list = array($exec_list);

            $call_params = ['output' => $output, 'rel_input' => $treeview_input, 'rel_session' => $treeview_session, 'capture' => $capture, 'parameters' => $parameters];

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
            $exec_list = $this->my_url_map->get('/exec');
            foreach ($exec_list as $path => $exec_spec_list) {
                if (!is_array($exec_spec_list))
                    $exec_spec_list = array($exec_spec_list);

                $output_view = $treeview_output->view($path);
                $input_view = $treeview_input->view($path);
                $session_view = $treeview_session->view($path);

                $call_params = ['rel_output' => $output_view, 'rel_input' => $input_view, 'rel_session' => $session_view, 'context_path' => $path, 'capture' => $capture, 'parameters' => $parameters];

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


        //dynamic template

        if ($this->my_url_map->is_set('/dynamic_template')) {
            $dynamic_template_spec = $this->my_url_map->get('/dynamic_template');
            if (!is_string($dynamic_template_spec)) {
                $errors['dynamic_template'][] = "Unable to execute dynamic template call : value is not a string.";
            } else {
                $dynamic = new LDynamicCall();

                $call_params = ['output' => $output, 'input' => $treeview_input, 'session' => $treeview_session, 'capture' => $capture, 'parameters' => $parameters];
                try {
                    $dynamic->saveIntoTemplate($dynamic_template_spec, $call_params, $this->my_url_map);
                } catch (\Exception $ex) {
                    LErrorList::saveFromException('dynamic_template', $ex);
                }
            }
        }


        //template rendering

        if ($this->my_url_map->is_set('/template')) {
            $template_path = $this->my_url_map->get('/template');

            $renderer = new LTemplateRendering($this->my_url_map, $treeview_input, $treeview_session, $capture, $parameters, $output);

            $result = $renderer->render($template_path);

            if ($result)
                return $result;
        }


        if ($this->my_url_map->is_set('/format')) {
            $format = $this->my_url_map->get('/format');

            if ($format == LFormat::JSON) {

                return $this->jsonEncodeResult($output);
            }
        }

        return $output;
    }

}
