<?php

class LConditionTest extends LTestCase {
    
    function testCondition1() {
        
        $cond = array("a" => "a");
        
        $c = new LCondition();
        
        $this->assertTrue($c->evaluate($cond),"La condizione non funziona!");
        
    }
    
    function testCondition2() {
        
        $cond = array("a" => [2,"a"]);
        
        $c = new LCondition();
        
        $this->assertTrue($c->evaluate($cond),"La condizione non funziona!");
        
    }
    
    function testCondition3() {
        
        $cond = array("a" => [2,"a"],"b" => ["a","c"]);
        
        $c = new LCondition();
        
        $this->assertFalse($c->evaluate($cond),"La condizione non funziona!");
        
    }
    
    function testCondition4() {
        
        $cond = true;
        
        $c = new LCondition();
                
        $this->assertTrue($c->evaluate($cond),"La condizione non funziona!");
        
    }
    
    function testCondition5() {
        
        $cond = false;
        
        $c = new LCondition();
                
        $this->assertFalse($c->evaluate($cond),"La condizione non funziona!");
        
    }
    
}
