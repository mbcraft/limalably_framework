<?php

class CallExecutorTest extends LTestCase {
    
    function testCallProc() {
        
        $e = new LCallExecutor();
        
        $e->init($_SERVER['FRAMEWORK_DIR'],"tests/",".php");
        
        $result = $e->execute("/urlmap/sample_file_42");
        
        $this->assertTrue(is_array($result),"Il risultato ottenuto non è un array!");
        $this->assertEqual($result[0],42,"Il risultato atteso non corrisponde!");
        
    }
    
    function testCallObjectMethod() {
        $e = new LCallExecutor();
        
        $e->init($_SERVER['FRAMEWORK_DIR'],"tests/",".php");
        
        LInput::set("prova", 10);
        
        $result = $e->execute("CallExecutorTest#myMethodOk");
        
        $this->assertTrue(is_array($result),"Il risultato ottenuto non è un array!");
        $this->assertEqual($result[0],10,"Il risultato atteso non corrisponde!");
        
        LInput::clear();
    }
    
    function testCallStaticMethod() {
        $e = new LCallExecutor();
        
        $e->init($_SERVER['FRAMEWORK_DIR'],"tests/",".php");
        
        LInput::set("ancora", 1);
        LInput::set("qualcosa",2);
        LInput::set("val",3);
        
        $result = $e->execute("CallExecutorTest::myStaticMethodOk");
        
        $this->assertTrue(is_array($result),"Il risultato ottenuto non è un array!");
        $this->assertEqual($result[0],1,"Il risultato atteso non corrisponde!");
        $this->assertEqual($result[1],2,"Il risultato atteso non corrisponde!");
        $this->assertEqual($result[2],3,"Il risultato atteso non corrisponde!");
        $this->assertEqual(count($result),3,"Il numero di elementi nel risultato non corrisponde!");
        
        LInput::clear();
    }
    
    function myMethodOk($prova,$qualcosa=10) {
        return [$prova];
    }
    
    static function myStaticMethodOk($ancora,$qualcosa,$val=10) {
        
        return [$ancora,$qualcosa,$val];
        
    }
    
}
