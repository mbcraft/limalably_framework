<?php

class ValidationDriverTest extends LTestCase {
    
    function testOkValidation() {
        
        $v = new LRespectValidationDriver();
        
        $v->validate("ciccio", "prova", "Length(0,10)");
        
    }
    
    function testFailedValidation() {
        
        $v = new LRespectValidationDriver();
        
        try {
            $v->validate("ciccio", "prova", ["Length(2,4)"]);
            
            $this->fail("Non è stata lanciata un'eccezione come invece previsto!");
            
        } catch (Exception $ex) {
            LResult::framework_debug($ex->getMessage());
        }
        
    }
    
    function testMultipleFailures() {
        
        $v = new LRespectValidationDriver();
        
        try {
            $v->validate("ciccio", "prova", ["Length(2,4)","IntVal"]);
            
            $this->fail("Non è stata lanciata un'eccezione come invece previsto!");
            
        } catch (Exception $ex) {
            var_dump($ex->getMessages());
        }
        
    }
    
}
