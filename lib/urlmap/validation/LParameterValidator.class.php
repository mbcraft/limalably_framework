<?php

/*
Aggiungere gestione condition
Aggiungere lettura automatica dei vari parametri dall'array, no parametri multipli nel costruttore
 */
class LParameterValidator {

    private $type;
    private $name;
    private $is_set;
    private $value;
    private $condition;
    private $neg_condition;
    private $rules;
    private $has_default_value;
    private $default_value;
    
    function __construct($type,$name,$is_set,$value,$parameters) {
        $this->type = $type;
        $this->name = $name;
        $this->is_set = $is_set;
        $this->value = $value;
        
        $this->condition = array_key_exists('conditions',$parameters) ? $parameters['conditions'] : [];
        $this->neg_condition = array_key_exists('!conditions',$parameters) ? $parameters['!conditions'] : [];
        
        $this->rules = array_key_exists('rules',$parameters) ? $parameters['rules'] : [];
        
        $this->has_default_value = array_key_exists('default_value',$parameters);
        $this->default_value = array_key_exists('default_value',$parameters) ? $parameters['default_value'] : null;
    }
    
    function validate($treeview_input,$treeview_session) {
        
        $condition = new LCondition();
        $evaluate_rules = $condition->evaluate($this->type,$this->condition);
        $skip_rules = $condition->evaluate($this->type, $this->neg_condition);
        
        if ($evaluate_rules && !$skip_rules) {

            $driver_class_name = LConfigReader::simple('/urlmap/validation_driver_class');
            $driver_instance = new $driver_class_name();

            $validated_value = $this->is_set ? $this->value : $this->default_value;
            
            return $driver_instance->validate($this->name,$validated_value,$this->rules,$treeview_input,$treeview_session);
        } else {
            if (!$this->is_set && !$this->has_default_value) return ['Default value not found for missing '.$this->type.' parameter : '.$this->name];
            else return [];
        }
 
        
    }
        
    function getNormalizedValue() {
        if (!$this->is_set && $this->has_default_value) return $this->default_value;
        if (in_array("BoolVal", $this->rules)) return filter_var($this->value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE); //ok cerca nei valori
        return $this->value;
    }
}