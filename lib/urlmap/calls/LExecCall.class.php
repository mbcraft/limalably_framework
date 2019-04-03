<?php

class LExecCall {
    
    const REPLACE_DATA_CALL_OPTION_SUFFIX = '!';
    
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
    
    public function execute(string $call_spec,array $all_param_data) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (LStringUtils::endsWith($call_spec,self::REPLACE_DATA_CALL_OPTION_SUFFIX)) {
            $use_replace = true;
            $my_call_spec = substr($call_spec,0,-1);
        } else {
            $use_replace = false;
            $my_call_spec = $call_spec;
        }
                
        $result = $this->my_call->execute($my_call_spec,$all_param_data,false);
                
        if ($use_replace) {
            $all_param_data['output']->replace("",$result);
        } else {
            $all_param_data['output']->merge("",$result);
        }
        
    }
    
    
}