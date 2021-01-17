<?php

/**
 * Description of TreeMap2Test
 *
 * @author Marco Bagnaresi <marco.bagnaresi@gmail.com>
 */
class TreeMap2Test extends LTestCase{
    function testRootOperationsAdd() {
        
        $output = new LTreeMap();
        
        $output->add("/", [42]);
        
        $this->assertEqual($output->get("/")[0][0],42 , "Il risultato dell'operazione di add non coincide!");
        
        
    }
    
    function testRootOperationsSet()  {
        $output = new LTreeMap();
        
        $output->set("/", [42]);
        
        $this->assertEqual($output->get("/")[0],42 , "Il risultato dell'operazione di set non coincide! : ".print_r($output->get("/"),true));
        
    }
    
    function testPathTokensSlash() {
        
        $path = "/";
        
        $path_tokens = LTreeMap::path_tokens($path);
        
        $this->assertEqual(count($path_tokens), 0, "Il numero di path token non corrisponde!");
        
        $last_path_token = LTreeMap::last_path_token($path);
        
        $this->assertNull($last_path_token, "Il last path token non è null!");
    }
    
}
