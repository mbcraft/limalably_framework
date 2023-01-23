<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class ValidationDriverTest extends LTestCase {
    
    static function defineRequiredClass() {
        return "Respect\Validation\Rules\AllOf";
    }

    function testOkValidation() {
        
        $v = new LRespectValidationDriver();
        
        $result = $v->validate("ciccio", "prova", "Length(0,10)",null,null);
        
        $this->assertTrue(count($result)==0,"Sono stati ritornati dei messaggi d'errore!");
        
    }
    
    function testFailedValidation() {
        
        $v = new LRespectValidationDriver();
        
        $result = $v->validate("ciccio", "prova", ["Length(2,4)"],null,null);
            
        $this->assertTrue(count($result)>0,"Non ci sono messaggi d'errore risultanti!");       
        
    }

    function testFailedValidationAgain() {
    
        $v = new LRespectValidationDriver();
        
        $result = $v->validate("ciccio_is_the_name_of_validation", "prova", ["Length(2,4)"],null,null);
            
        $this->assertTrue(count($result)>0,"Non ci sono messaggi d'errore risultanti!");       
        
    }
    
    function testMultipleFailures() {
        
        $v = new LRespectValidationDriver();
        
        $result = $v->validate("ciccio", "prova", ["Length(2,4)","IntVal"],null,null);
            
        $this->assertTrue(count($result)>0,"Non ci sono messaggi d'errore risultanti!");    
    }
    
    
}
