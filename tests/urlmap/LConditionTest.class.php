<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class LConditionTest extends LTestCase {
    
    function testCondition1() {
        
        $cond = array("exec_mode" => ["m","fd","d","t"]);
        
        $c = new LCondition();
        
        $this->assertTrue($c->evaluate('test',$cond),"La condizione non funziona!");
        
    }
    
    function testCondition2() {
        
        $cond = array("language" => [2,"a"]);
        
        $c = new LCondition();
        
        $this->assertFalse($c->evaluate('test',$cond),"La condizione non funziona!");
        
    }
    
    function testCondition3() {
        
        $cond = array("request_method" => [2,"cli"],"environment" => ["a","script"]);
        
        $c = new LCondition();
        
        $this->assertTrue($c->evaluate('test',$cond),"La condizione non funziona!");
        
    }
    
    function testCondition4() {
        
        $cond = true;
        
        $c = new LCondition();
                
        $this->assertTrue($c->evaluate('test',$cond),"La condizione non funziona!");
        
    }
    
    function testCondition5() {
        
        $cond = false;
        
        $c = new LCondition();
                
        $this->assertFalse($c->evaluate('test',$cond),"La condizione non funziona!");
        
    }
    
}
