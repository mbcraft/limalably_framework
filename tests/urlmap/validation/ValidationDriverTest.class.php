<?php

class ValidationDriverTest extends LTestCase {
    
    function testOkValidation() {
        
        $v = new LRespectValidationDriver();
        
        $result = $v->validate("ciccio", "prova", "Length(0,10)");
        
        $this->assertTrue(count($result)==0,"Sono stati ritornati dei messaggi d'errore!");
        
    }
    
    function testFailedValidation() {
        
        $v = new LRespectValidationDriver();
        
        $result = $v->validate("ciccio", "prova", ["Length(2,4)"]);
            
        $this->assertTrue(count($result)>0,"Non ci sono messaggi d'errore risultanti!");       
        
    }
    
    function testMultipleFailures() {
        
        $v = new LRespectValidationDriver();
        
        $result = $v->validate("ciccio", "prova", ["Length(2,4)","IntVal"]);
            
        $this->assertTrue(count($result)>0,"Non ci sono messaggi d'errore risultanti!");    
    }
    
}
