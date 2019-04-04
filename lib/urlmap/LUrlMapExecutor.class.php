<?php

class LUrlMapExecutor {

    private $my_url_map = null;

    function __construct($url_map) {
        if (!$url_map instanceof LTreeMap)
            throw new \Exception("Url map is not valid");
        $this->my_url_map = $url_map;
    }

    function executeRootRequest() {

        $parameters = isset($_SERVER['PARAMETERS']) ? $_SERVER['PARAMETERS'] : [];
        $in = LInputUtils::create();
    }

    function execute($route, $parameters, $capture, $treeview_input, $treeview_session) {

        $output = new LTreeMap();
        $output->set('/success', true);
        $treeview_output = $output->view('/');
        $error_list = new LErrorList();
        //loading prepared input
        if ($this->my_url_map->is_set('/load')) {
            try {
                $input_loader = new LInputLoader();
                $input_loader->loadDataInTreeMap($this->my_url_map->get('/load'), $treeview_input);
            } catch (\Exception $ex) {
                $error_list->saveFromException('load', $ex);
            }
        }

        //input parameters check
        if ($error_list->continueExecution()) {
            if ($this->my_url_map->is_set('/input')) {
                $input_validator = new LParameterGroupValidator($treeview_input, $this->my_url_map->get('/input'));
                $error_list->saveFromData('input', $input_validator->validate($treeview_input, $treeview_session));
            }
        }
        //session parameters check
        if ($error_list->continueExecution()) {
            if ($this->my_url_map->is_set('/session')) {
                $session_validator = new LParameterGroupValidator($treeview_session, $this->my_url_map->get('/session'));
                $error_list->saveFromData('session', $session_validator->validate($treeview_input, $treeview_session));
            }
        }

        if ($error_list->continueExecution()) {

            //capture resolution
            if ($this->my_url_map->is_set('/capture')) {
                try {
                    $capture_resolver = new LRouteCapture();
                    $capture_pattern = $this->my_url_map->get('/capture');
                    $capture = $capture_resolver->captureParameters($capture_pattern, $route);
                } catch (\Exception $ex) {
                    $error_list->saveFromException('capture', $ex);
                }
            } else {
                $capture = [];
            }
        }

        //dynamic exec
        if ($error_list->continueExecution()) {
            if ($this->my_url_map->is_set('/dynamic_exec')) {
                $exec_list = $this->my_url_map->get('/dynamic_exec');
                if (!is_array($exec_list))
                    $exec_list = array($exec_list);

                $call_params = ['output' => $output, 'input' => $treeview_input, 'session' => $treeview_session, 'capture' => $capture, 'parameters' => $parameters];

                $dynamic = new LDynamicCall();

                foreach ($exec_list as $call_spec) {
                    try {
                        $dynamic->saveIntoExec($call_spec, $call_params, $this->my_url_map);
                    } catch (\Exception $ex) {
                        $error_list->saveFromException('dynamic_exec', $ex);
                    }
                }
            }
        }

        //exec tree
        if ($error_list->continueExecution()) {
            if ($this->my_url_map->is_set('/exec')) {
                $exec_list = $this->my_url_map->get('/exec');
                foreach ($exec_list as $path => $exec_spec_list) {
                    if (!is_array($exec_spec_list))
                        $exec_spec_list = array($exec_spec_list);

                    $output_view = $treeview_output->view($path);
                    $input_view = $treeview_input->view($path);
                    $session_view = $treeview_session->view($path);

                    $call_params = ['output' => $output_view, 'input' => $input_view, 'session' => $session_view, 'context_path' => $path, 'capture' => $capture, 'parameters' => $parameters];

                    foreach ($exec_spec_list as $call_spec) {
                        $executor = new LExecCall();
                        try {
                            $exec_error_list = $executor->execute($call_spec, $call_params);
                            if ($exec_error_list) {
                                $error_list->merge($exec_error_list);
                            }
                            if (!$error_list->continueExecution())
                                break 2;
                        } catch (\Exception $ex) {
                            $error_list->saveFromException('exec', $ex);
                        }
                    }
                }
            }
        }

        //dynamic template
        if ($error_list->continueExecution()) {
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
                        $error_list->saveFromException('dynamic_template', $ex);
                    }
                }
            }
        }

        //template rendering
        if ($error_list->continueExecution()) {
            if ($this->my_url_map->is_set('/template')) {
                $template_path = $this->my_url_map->get('/template');

                $template_factory = new LUrlMapTemplateSourceFactory();

                $template_source = $template_factory->createFileTemplateSource();

                if (!$template_source->hasTemplate($template_path)) {
                    $error_list->saveFromData('template', 'Unable to file template at path : ' . $template_path);
                } else {
                    $template = $template_source->getTemplate($template_path);

                    //inserire fra le variabili : urlmap, input, session, capture, i18n, parameters - con eventuale prefisso di path tipo 'meta'
                    $import_into_variables = LConfigReader::simple('/template/import_into_variables');

                    try {
                        foreach ($import_into_variables as $import_name) {
                            switch ($import_name) {
                                case 'urlmap' : $output->set('urlmap', $this->my_url_map->get('.'));
                                    break;
                                case 'input' : $output->set('input', $treeview_input->get('.'));
                                    break;
                                case 'session' : $output->set('session', $treeview_session->get('.'));
                                    break;
                                case 'parameters' : $output->set('parameters', $parameters);
                                    break;
                                case 'capture' : $output->set('capture', $capture);
                                    break;
                                case 'env' : $output->set('env', LEnvironmentUtils::getReplacementsArray());
                                    break;
                                case 'i18n' : throw new \Exception("i18n not implemented yet");
                                    break;

                                default : throw new \Exception("Unable to import into variables : " . $import_name);
                            }
                        }

                        return $template->render($output->getRoot());
                    } catch (\Exception $ex) {
                        $error_list->saveFromException('template', $ex);
                    }
                }
            }
        }

        if ($error_list->hasErrors()) {
            return $error_list;
        }
        
        if ($this->my_url_map->is_set('/format')) {
            $format = $this->my_url_map->get('/format');

            if ($format == LFormat::JSON) {

                $encode_options_list = LConfigReader::simple('/format/json/encode_options');
                $encode_options = 0;
                foreach ($encode_options_list as $enc_opt) {
                    try {
                        $encode_options |= eval('return JSON_' . $enc_opt . ';');
                    } catch (\Exception $ex) {
                        $error_list->saveFromData('format', 'Invalid json encode format : JSON_' . $enc_opt . ' does not evaluate to an integer value.');
                    }
                }

                $error_list->mergeIntoTreeMap($output);
                $output_data = $output->getRoot();

                try {
                    return json_encode($output_data, $encode_options);
                } catch (\Exception $ex) {
                    $error_list->saveFromException('format', $ex);
                }
            }
        }

        if ($error_list->hasErrors()) {
            return $error_list;
        }

        return $output;
    }

}
