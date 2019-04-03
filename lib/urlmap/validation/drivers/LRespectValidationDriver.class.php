<?php

class LRespectValidationDriver implements LIValidatorDriver {
    
    /*
    This method permits the use of four additional rules : EqualsInput, EqualsSession, IdenticalInput, IdenticalSession
     */
    private function prepareRule($rule_spec) {
        if (LStringUtils::startsWith($rule_spec, "EqualsInput")) {
            if (LStringUtils::contains($rule_spec, "'")) $rule_spec = str_replace ("'", '', $rule_spec);
            if (LStringUtils::contains($rule_spec, '"')) $rule_spec = str_replace ('"', '', $rule_spec);
            $rule_spec = str_replace('(', '', $rule_spec);
            $rule_spec = str_replace(')', '', $rule_spec);
            
            return "Equals(\$input_map->get('".$rule_spec."'))";
        }
        if (LStringUtils::startsWith($rule_spec, "EqualsSession")) {
            if (LStringUtils::contains($rule_spec, "'")) $rule_spec = str_replace ("'", '', $rule_spec);
            if (LStringUtils::contains($rule_spec, '"')) $rule_spec = str_replace ('"', '', $rule_spec);
            $rule_spec = str_replace('(', '', $rule_spec);
            $rule_spec = str_replace(')', '', $rule_spec);
            
            return "Equals(\$session_map->get('".$rule_spec."'))";
        }
        if (LStringUtils::startsWith($rule_spec, "IdenticalInput")) {
            if (LStringUtils::contains($rule_spec, "'")) $rule_spec = str_replace ("'", '', $rule_spec);
            if (LStringUtils::contains($rule_spec, '"')) $rule_spec = str_replace ('"', '', $rule_spec);
            $rule_spec = str_replace('(', '', $rule_spec);
            $rule_spec = str_replace(')', '', $rule_spec);
            
            return "Identical(\$input_map->get('".$rule_spec."'))";
        }
        if (LStringUtils::startsWith($rule_spec, "IdenticalSession")) {
            if (LStringUtils::contains($rule_spec, "'")) $rule_spec = str_replace ("'", '', $rule_spec);
            if (LStringUtils::contains($rule_spec, '"')) $rule_spec = str_replace ('"', '', $rule_spec);
            $rule_spec = str_replace('(', '', $rule_spec);
            $rule_spec = str_replace(')', '', $rule_spec);
            
            return "Identical(\$session_map->get('".$rule_spec."'))";
        }
        
        return $rule_spec;
    }
    
    public function validate($name, $value, $rules,$input_map,$session_map) {
        $errors = [];
        $root_rule = new Respect\Validation\Rules\AllOf();
        
        if (!is_array($rules)) $rules = [$rules];
        
        foreach ($rules as $rule_spec) {
            $final_rule_spec = $this->prepareRule($rule_spec);
            try {
            $validator_instance = eval('return new Respect\\Validation\\Rules\\'.$final_rule_spec.';');
            } catch (\Exception $ex) {
                $errors[] = "Unable to istantiate rule for param ".$name.".Rule spec is : ".$rule_spec;
            }
            $root_rule->addRule($validator_instance);
            
        }
        
        $root_rule->setName($name);
        
        try {
            $root_rule->assert($value);
            return $errors;
        } catch (\Exception $ex) {
            $errors = $errors + $ex->getMessages();
        }
        return $errors;
        
    }

}