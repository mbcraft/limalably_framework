<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LParametersSetup {
	
    const AVAILABLE_IMPORTS = ['basedir','urlmap', 'urlmap_string', 'relative_input', 'relative_input_string', 'absolute_input', 'absolute_input_string', 'relative_session', 'relative_session_string', 'absolute_session', 'absolute_session_string', 'parameters', 'parameters_string', 'capture', 'capture_string', 'env', 'env_string', 'output_string', 'flash'];

    private $my_urlmap = null;
    private $my_input = null;
    private $my_session = null;
    private $my_capture = null;
    private $my_parameters = null;
    private $my_output = null;

    private $engine_name = null;
    
    function __construct($urlmap, $input, $session, $capture, $parameters, $output) {
        $this->my_urlmap = $urlmap;
        $this->my_input = $input;
        $this->my_session = $session;
        $this->my_capture = $capture;
        $this->my_parameters = $parameters;
        $this->my_output = $output;
        
    }

    public function findEngineName() {

        $this->engine_name = "twig";

        if (isset($this->my_urlmap)) {
            if ($this->my_urlmap->is_set('/template/engine'))  
            { 
                $engine = $this->my_urlmap->get('/template/engine');

                $this->engine_name = LTemplateUtils::findTemplateSourceFactoryName($engine);

                return;
            } 

            if ($this->my_urlmap->is_set('/template/name')) {
                $template_name = $this->my_urlmap->get('/template/name');

                $engine_list = LConfigReader::simple('/template');

                foreach ($engine_list as $engine_name => $engine_specs) {
                    $extension_search_list = $engine_specs['extension_search_list'];

                    foreach ($extension_search_list as $ext) {
                        if (LStringUtils::endsWith($template_name,$ext)) {
                            $this->engine_name = $engine_name;
                            return;
                        }
                    }
                }

                throw new \Exception("No suitable engine found for template : ".$template_name);
            }
        } else {
            $this->engine_name = "twig";
        }
    }

    public function getEngineName() {
    	return $this->engine_name;
    }

    private function my_json_encode($name, $value) {
        try {
            return json_encode($value, JSON_PRETTY_PRINT);
        } catch (\Exception $ex) {
            LErrorList::saveFromErrors('template', "Unable to render as json the " . $name . " variable without errors!!");
        }
    }

    public function getAllParameters() {
    	return $this->my_output->getRoot();
    }

    public function setupParameters() {
		//inserire fra le variabili : urlmap, input, session, capture, i18n, parameters - con eventuale prefisso di path tipo 'meta'
        $import_into_variables = LConfigReader::executionMode('/template/'.$this->engine_name.'/import_into_variables');
            
        if (!$this->my_output) $this->my_output = new LTreeMap();
            //

        //output_string goes before all the others
        if (in_array('output_string', $import_into_variables)) { //ok cerca nei valori
            if ($this->my_output) $this->my_output->set('output_string', $this->my_json_encode('output', $this->my_output->getRoot()));
        }
        //import all the other variables
        foreach ($import_into_variables as $import_name) {
            switch ($import_name) {
                case 'basedir' : {
                    $basedir = LConfigReader::simple('/misc/basedir');
                    $this->my_output->set('bd',$basedir);
                    $this->my_output->set('basedir',$basedir);
                    break;
                }
                case 'urlmap' : if ($this->my_urlmap) $this->my_output->set('urlmap', $this->my_urlmap->get('/'));
                    break;
                case 'urlmap_string' : if ($this->my_urlmap) $this->my_output->set('urlmap_string', $this->my_json_encode('urlmap', $this->my_urlmap->get('/')));
                    break;
                case 'rel_input' : if ($this->my_input) $this->my_output->set('rel_input', $this->my_input->get('.'));
                    break;
                case 'rel_input_string' : if ($this->my_input) $this->my_output->set('rel_input_string', $this->my_json_encode('input', $this->my_input->get('.')));
                    break;
                case 'input' : if ($this->my_input) $this->my_output->set('input', $this->my_input->get('/'));
                    break;
                case 'input_string' : if ($this->my_input) $this->my_output->set('input_string', $this->my_json_encode('input', $this->my_input->get('/')));
                    break;
                case 'rel_session' : if ($this->my_session) $this->my_output->set('rel_session', $this->my_session->get('.'));
                    break;
                case 'rel_session_string' : if ($this->my_session) $this->my_output->set('rel_session_string', $this->my_json_encode('session', $this->my_session->get('.')));
                    break;
                case 'session' : if ($this->my_session) $this->my_output->set('session', $this->my_session->get('/'));
                    break;
                case 'session_string' : if ($this->my_session) $this->my_output->set('session_string', $this->my_json_encode('session', $this->my_session->get('/')));
                    break;
                case 'parameters' : if ($this->my_parameters) $this->my_output->set('parameters', $this->my_parameters);
                    break;
                case 'parameters_string' : if ($this->my_parameters) $this->my_output->set('parameters_string', $this->my_json_encode('parameters', $this->my_parameters));
                    break;
                case 'capture' : if ($this->my_capture) $this->my_output->set('capture', $this->my_capture);
                    break;
                case 'capture_string' : if ($this->my_capture) $this->my_output->set('capture_string', $this->my_json_encode('capture', $this->my_capture));
                    break;
                case 'env' : $this->my_output->set('env', LEnvironmentUtils::getReplacementsArray());
                    break;
                case 'env_string' : $this->my_output->set('env_string', $this->my_json_encode('env', LEnvironmentUtils::getReplacementsArray()));
                    break;
                case 'i18n' : $this->my_output->set('i18n', LI18nUtils::getCurrentLangData()); 
                   break;
                case 'flags' : $this->my_output->set('flags', array('debug_enabled' => LConfigReader::executionMode('/misc/debug_enabled'),'trace_enabled' => LConfigReader::executionMode('/misc/trace_enabled'),'introspection_enabled' => LConfigReader::executionMode('/misc/introspection_enabled'))); 
                    break;
                case 'flash' : $this->my_output->set('flash', LFlash::getAllMessages()); 
                    break;
                case 'output_string' : break; //already done to avoid output accumulation

                default : throw new \Exception("Unable to import into variables : " . $import_name . " .Available imports : " . var_export(self::AVAILABLE_IMPORTS, true));
            }
        }
    }
}