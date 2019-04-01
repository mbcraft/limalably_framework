<?php

class LRespectValidationDriver implements LIValidatorDriver {
    
    public function validate($name, $value, $rules) {
        
        $root_rule = new Respect\Validation\Rules\AllOf();
        
        if (!is_array($rules)) $rules = [$rules];
        
        foreach ($rules as $rule_spec) {
            try {
            $validator_instance = eval('return new Respect\\Validation\\Rules\\'.$rule_spec.';');
            } catch (\Exception $ex) {
                return ["Unable to istantiate rule for param ".$name.".Rule spec is : ".$rule_spec];
            }
            $root_rule->addRule($validator_instance);
            
        }
        
        $root_rule->setName($name);
        
        try {
            $root_rule->assert($value);
            return [];
        } catch (\Exception $ex) {
            return $ex->getMessages();
        }
        
    }

}