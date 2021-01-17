<?php

class ExecCallTest extends LTestCase {
    
    function testCallProc() {
        
        $e = new LExecCall();
        
        $e->init($_SERVER['FRAMEWORK_DIR'],"tests/",".php",'tests/data/');
        
        $output = new LTreeMap();
        
        $params = ["rel_input" => new LTreeMap(),"rel_session" => new LTreeMap(),"rel_output" => $output];
        
        $e->execute("/urlmap/sample_file_42",$params,false);
        
        $this->assertTrue(is_array($output->get("/")),"Il risultato ottenuto non è un array!");
        
        $this->assertEqual($output->get("/")[0],42,"Il risultato atteso non corrisponde! : ".print_r($output,true));
        
    }
    
    function testCallObjectMethod() {
        $e = new LExecCall();
        
        $e->init($_SERVER['FRAMEWORK_DIR'],"tests/",".php",'tests/data/');
        
        $input = new LTreeMap();
        $input->set("prova", 10);
        
        $output = new LTreeMap();
        
        $params = ["rel_input" => $input,"rel_session" => new LTreeMap(),"rel_output" => $output];
        
        $e->execute("ExecCallTest#myMethodOk",$params,false);
        
        $this->assertTrue(is_array($output->get("/")),"Il risultato ottenuto non è un array!");
        $this->assertEqual($output->get("/")[0],10,"Il risultato atteso non corrisponde! : ".print_r($output->get('/'),true));
        
    }
    
    function testCallStaticMethod() {
        $e = new LExecCall();
        
        $e->init($_SERVER['FRAMEWORK_DIR'],"tests/",".php",'tests/data/');
        
        $input = new LTreeMap();
        
        $input->set("ancora", 1);
        $input->set("qualcosa",2);
        $input->set("val",3);
        
        $output = new LTreeMap();
        
        $params = ["rel_input" => $input,"rel_session" => new LTreeMap(),"rel_output" => $output];
        
        $e->execute("ExecCallTest::myStaticMethodOk",$params,false);
        
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
