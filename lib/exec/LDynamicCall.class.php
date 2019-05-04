<?php

class LDynamicCall {
    
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
                        
        $result = $this->my_call->execute($call_spec,$all_param_data,true);
        
        if (!is_string($result)) throw new \Exception("Dynamic template result is not a valid string.");
        
        $this->urlmap_tree->set('/template',$result);
    }
    
    function saveIntoExec($call_spec,$all_param_data,$urlmap_tree,bool $add) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
                
        $call = new LCall();
        
        $result = $call->execute($call_spec,$all_param_data,true);
        
        if (!is_array($result)) throw new \Exception("Dynamic exec result is not a valid array");
        
        if ($add) {
            foreach ($result as $key => $value) {
                $urlmap_tree->add('/exec/'.$key,$value);
            }
        } else {
            foreach ($result as $key => $value) {
                $urlmap_tree->set('/exec/'.$key,$value);
            }
        }
    }
    
}
