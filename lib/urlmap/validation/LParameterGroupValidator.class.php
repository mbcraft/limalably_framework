<?php

class LParameterGroupValidator {
    
    private $treeview_params;
    private $validation_list;
    
    function __construct($treeview_params,$validation_list) {
        
        $this->treeview_params = $treeview_params;
        $this->validation_list = $validation_list;
        
    }
    
    function validate($treeview_input,$treeview_session) {
        
        $final_result = [];
        
        foreach ($this->validation_list as $name => $params) {
            
            $is_set = $this->treeview_params->is_set($name);
            if ($is_set) $value = $this->treeview_params->get($name);
            else $value = null;
            
            $validator = new LParameterValidator($name,$is_set,$value,$params);
            
            $final_result = array_merge($final_result,$validator->validate($treeview_input,$treeview_session));
            
            $this->treeview_params->set($name,$validator->getNormalizedValue());
        }
        
        return $final_result;
        
    }
    
}