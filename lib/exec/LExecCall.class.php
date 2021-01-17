<?php

class LExecCall {
    
    private $my_call = null;
    
    function isInitialized() {
        return $this->my_call->isInitialized();
    }
    
    public function init($base_dir,$proc_folder,$proc_extension,$data_folder) { 
        $this->my_call->init($base_dir,$proc_folder,$proc_extension,$data_folder);
    }
    
    public function initWithDefaults() {
        $this->my_call->initWithDefaults();
    }
    
    function __construct() {
        $this->my_call = new LCall();
    }
    
    public function execute(string $call_spec,array $all_param_data,bool $add) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
                        
        $result = $this->my_call->execute($call_spec,$all_param_data,false);
        
        if (is_null($result)) return;
        
        if ($result instanceof LTreeMap) {
            $result = $result->get('/');
        }
        
        if ($result instanceof LTreeMapView) {
            $result = $result->get('.');
        }
        
        $my_output = $all_param_data['rel_output'];
        
        if ((!$my_output instanceof LTreeMap) && (!$my_output instanceof LTreeMapView)) throw new \Exception("A TreeMap or TreeMapView is needed for output!");
        
        if ($my_output instanceof LTreeMap) $my_output_path = '/';
        if ($my_output instanceof LTreeMapView) $my_output_path = '.';
        
        if ($add) {    
            $all_param_data['rel_output']->add($my_output_path,$result);
        } else {
            $all_param_data['rel_output']->set($my_output_path,$result);
        }
        
    }
    
    
}