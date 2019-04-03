<?php

class LErrorList {
    
    private $data = [];
    
    public function saveFromException(string $type,\Exception $ex) {
        $this->data[$type] = $ex->getMessage();
    }
    
    public function saveFromData(string $type,$errors) {
        if (is_string($errors)) $errors = [$errors];
        
        $this->data = array_merge_recursive($this->data,array($type => $errors));
    }
    
    public function hasErrors() {
        return !empty($this->data);
    }
    
    public function continueExecution() {
        return !$this->hasErrors();
    }
    
    public function merge(\ErrorList $error_list) {
        $this->data = array_merge_recursive($error_list->data);
    }
    
    public function mergeIntoTreeMap($treemap) {
        if ($this->hasErrors()) {
            $treemap->set('/success',false);
            $treemap->set('/errors',$this->data);
        } else {
            $treemap->set('/success',true);
        }
    }
    
}
