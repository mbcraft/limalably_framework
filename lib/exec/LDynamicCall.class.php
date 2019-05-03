<?php

class LDynamicCall {
    
    const MERGE_DATA_CALL_OPTION_SUFFIX = ',';
    
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
    
    function saveIntoTemplate($call_spec,$all_param_data,$urlmap_tree) {
        if ($urlmap_tree->is_set('/template')) throw new \Exception("Urlmap has already a template, can't use dynamic_template.");
        
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (LStringUtils::endsWith($call_spec,self::MERGE_DATA_CALL_OPTION_SUFFIX)) {
            $use_replace = false;
            $my_call_spec = substr($call_spec,0,-1);
        } else {
            $use_replace = true;
            $my_call_spec = $call_spec;
        }
                
        $result = $this->my_call->execute($my_call_spec,$all_param_data,true);
        
        if (!is_string($result)) throw new \Exception("Dynamic template result is not a valid string.");
        
        $this->urlmap_tree->set('/template',$result);
    }
    
    function saveIntoExec($call_spec,$all_param_data,$urlmap_tree) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (LStringUtils::endsWith($call_spec,self::MERGE_DATA_CALL_OPTION_SUFFIX)) {
            $use_replace = false;
            $my_call_spec = substr($call_spec,0,-1);
        } else {
            $use_replace = true;
            $my_call_spec = $call_spec;
        }
        
        $call = new LCall();
        
        $result = $call->execute($my_call_spec,$all_param_data,true);
        
        if (!is_array($result)) throw new \Exception("Dynamic exec result is not a valid array");
        
        if ($use_replace) {
            foreach ($result as $key => $value) {
                $urlmap_tree->replace('/exec/'.$key,$value);
            }
        } else {
            foreach ($result as $key => $value) {
                $urlmap_tree->merge('/exec/'.$key,$value);
            }
        }
    }
    
}
