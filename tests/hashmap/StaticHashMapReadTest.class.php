<?php

class StaticHashMapReadTest extends LTestCase {
    function testPathTokensFunctions1()
    {
        $path = "/html/head/keywords";
        
        $path_tokens = LConfig::path_tokens($path);

        $this->assertEqual(count($path_tokens),3,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token non corrisponde");
        $this->assertEqual($path_tokens[1],"head","Il token non corrisponde");
        $this->assertEqual($path_tokens[2],"keywords","Il token non corrisponde");
        
        $last_token = LConfig::last_path_token($path);
        $this->assertEqual($last_token,"keywords","Il token non corrisponde");
        
        $all_but_last = LConfig::all_but_last_path_tokens($path);

        $this->assertEqual(count($all_but_last),2,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($all_but_last[0],"html","Il token non corrisponde");
        $this->assertEqual($all_but_last[1],"head","Il token non corrisponde");
        
    }
    
    function testPathTokensFunctions2()
    {
        $path = "/html";
        
       
        $path_tokens = LConfig::path_tokens($path);

        $this->assertEqual(count($path_tokens),1,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token non corrisponde");
        
        $last_token = LConfig::last_path_token($path);
        $this->assertEqual($last_token,"html","Il token non corrisponde");
        
        $all_but_last = LConfig::all_but_last_path_tokens($path);
 
        $this->assertEqual(count($all_but_last),0,"Il numero dei path token non corrisponde!!");

    }
    
    function testPathTokensFunctions3()
    {
        $path = "/html//";
        
        $path_tokens = LConfig::path_tokens($path);

        $this->assertEqual(count($path_tokens),1,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token non corrisponde");
        
        $last_token = LConfig::last_path_token($path);
        $this->assertEqual($last_token,"html","Il token non corrisponde");
        
        $all_but_last = LConfig::all_but_last_path_tokens($path);
 
        $this->assertEqual(count($all_but_last),0,"Il numero dei path token non corrisponde!!");

    }
    
    function testPathTokensFunctions4()
    {
        $path = "//html///head/keywords/";
        
        $path_tokens = LConfig::path_tokens($path);

        $this->assertEqual(count($path_tokens),3,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token non corrisponde");
        $this->assertEqual($path_tokens[1],"head","Il token non corrisponde");
        $this->assertEqual($path_tokens[2],"keywords","Il token non corrisponde");
        
        $last_token = LConfig::last_path_token($path);
        $this->assertEqual($last_token,"keywords","Il token non corrisponde");
        
        $all_but_last = LConfig::all_but_last_path_tokens($path);

        $this->assertEqual(count($all_but_last),2,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($all_but_last[0],"html","Il token non corrisponde");
        $this->assertEqual($all_but_last[1],"head","Il token non corrisponde");
        
    }
    
    function testBasicConfigReadData() {
        
        $value = LConfig::keys('/defaults');
        
        $this->assertTrue(is_array($value),"Il valore ritornato non è un array!");
        $this->assertEqual(count($value),3,"Il numero di chiavi non corrisponde!");
        
        $value2 = LConfig::mustGet('/defaults/routemap/hash_db/levels');
        
        $this->assertEqual($value2,5,"Il valore letto dalla configurazione non corrisponde!");
        
    }
}
