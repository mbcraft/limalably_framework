<?php

class ExecCallTest extends LTestCase {
    
    function testCallProc() {
        
        $e = new LExecCall();
        
        $e->init($_SERVER['FRAMEWORK_DIR'],"tests/",".php",'tests/data/');
        
        $output = new LTreeMap();
        
        $params = ["input" => new LTreeMap(),"session" => new LTreeMap(),"output" => $output];
        
        $e->execute("/urlmap/sample_file_42",$params);
        
        $this->assertTrue(is_array($output->get("/")),"Il risultato ottenuto non è un array!");
        
        $this->assertEqual($output->get("/")[0],42,"Il risultato atteso non corrisponde!");
        
    }
    
    function testCallObjectMethod() {
        $e = new LExecCall();
        
        $e->init($_SERVER['FRAMEWORK_DIR'],"tests/",".php",'tests/data/');
        
        $input = new LTreeMap();
        $input->set("prova", 10);
        
        $output = new LTreeMap();
        
        $params = ["input" => $input,"session" => new LTreeMap(),"output" => $output];
        
        $e->execute("ExecCallTest#myMethodOk",$params);
        
        $this->assertTrue(is_array($output->get("/")),"Il risultato ottenuto non è un array!");
        $this->assertEqual($output->get("/")[0],10,"Il risultato atteso non corrisponde!");
        
    }
    
    function testCallStaticMethod() {
        $e = new LExecCall();
        
        $e->init($_SERVER['FRAMEWORK_DIR'],"tests/",".php",'tests/data/');
        
        $input = new LTreeMap();
        
        $input->set("ancora", 1);
        $input->set("qualcosa",2);
        $input->set("val",3);
        
        $output = new LTreeMap();
        
        $params = ["input" => $input,"session" => new LTreeMap(),"output" => $output];
        
        $e->execute("ExecCallTest::myStaticMethodOk",$params);
        
        $this->assertTrue(is_array($output->get("/")),"Il risultato ottenuto non è un array!");
        $this->assertEqual($output->get("/")[0],1,"Il risultato atteso non corrisponde!");
        $this->assertEqual($output->get("/")[1],2,"Il risultato atteso non corrisponde!");
        $this->assertEqual($output->get("/")[2],3,"Il risultato atteso non corrisponde!");
        $this->assertEqual(count($output->get("/")),3,"Il numero di elementi nel risultato non corrisponde!");
        
        
    }
    
    function myMethodOk($prova,$qualcosa=10) {
        return [$prova];
    }
    
    static function myStaticMethodOk($ancora,$qualcosa,$val=10) {
        
        return [$ancora,$qualcosa,$val];
        
    }
    
}
