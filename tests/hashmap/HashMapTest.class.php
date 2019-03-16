<?php

class HashMapTest extends LTestCase {
    
    function testPathTokensFunctions1()
    {
        $path = "/html/head/keywords";
        
        $path_tokens = LHashMap::path_tokens($path);

        $this->assertEqual(count($path_tokens),3,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token del path non corrisponde");
        $this->assertEqual($path_tokens[1],"head","Il token del path non corrisponde");
        $this->assertEqual($path_tokens[2],"keywords","Il token del path non corrisponde");
        
        $last_token = LHashMap::last_path_token($path);
        $this->assertEqual($last_token,"keywords","Il token del path non corrisponde");
        
        $all_but_last = LHashMap::all_but_last_path_tokens($path);

        $this->assertEqual(count($all_but_last),2,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($all_but_last[0],"html","Il token del path non corrisponde");
        $this->assertEqual($all_but_last[1],"head","Il token del path non corrisponde");
        
    }
    
    function testPathTokensFunctions2()
    {
        $path = "/html";
        
       
        $path_tokens = LHashMap::path_tokens($path);

        $this->assertEqual(count($path_tokens),1,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token del path non corrisponde");
        
        $last_token = LHashMap::last_path_token($path);
        $this->assertEqual($last_token,"html","Il token del path non corrisponde");
        
        $all_but_last = LHashMap::all_but_last_path_tokens($path);
 
        $this->assertEqual(count($all_but_last),0,"Il numero dei path token non corrisponde!!");

    }
    
    function testPathTokensFunctions3()
    {
        $path = "/html//";
        
        $path_tokens = LHashMap::path_tokens($path);

        $this->assertEqual(count($path_tokens),1,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token del path non corrisponde");
        
        $last_token = LHashMap::last_path_token($path);
        $this->assertEqual($last_token,"html","Il token del path non corrisponde");
        
        $all_but_last = LHashMap::all_but_last_path_tokens($path);
 
        $this->assertEqual(count($all_but_last),0,"Il numero dei path token non corrisponde!!");

    }
    
    function testPathTokensFunctions4()
    {
        $path = "//html///head/keywords/";
        
        $path_tokens = LHashMap::path_tokens($path);

        $this->assertEqual(count($path_tokens),3,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token del path non corrisponde");
        $this->assertEqual($path_tokens[1],"head","Il token del path non corrisponde");
        $this->assertEqual($path_tokens[2],"keywords","Il token del path non corrisponde");
        
        $last_token = LHashMap::last_path_token($path);
        $this->assertEqual($last_token,"keywords","Il token del path non corrisponde");
        
        $all_but_last = LHashMap::all_but_last_path_tokens($path);

        $this->assertEqual(count($all_but_last),2,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($all_but_last[0],"html","Il token del path non corrisponde");
        $this->assertEqual($all_but_last[1],"head","Il token del path non corrisponde");
        
    }
    
    function testSimpleLevel1()
    {
        $r = new LHashMap();
        
        $r->set("/first","my_value");
        
        $this->assertTrue($r->is_set("/first"),"Il nodo first non e' stato creato!!");
        
        $this->assertEqual($r->get("/first"),"my_value","Il valore impostato non corrisponde!!");
        
        $r->clear();
        
        $this->assertFalse($r->is_set("/first"),"Il nodo first e' stato trovato!!");
        
    }
    
    function testAdd()
    {
        $r = new LHashMap();
        
        $r->add("/html/head/keywords","hello");
        
        $this->assertEqual(count($r->get("/html/head/keywords")),1,"Il numero di keywords non corrisponde!!");
        
        $r->add("/html/head/keywords","spank");
        
        $this->assertEqual(count($r->get("/html/head/keywords")),2,"Il numero di keywords non corrisponde!!");
    }
    
    function testRemove()
    {
        $r = new LHashMap();
        
        $r->set("/html/head/keywords",array("hello","world"));
        
        $this->assertEqual(count($r->get("/html/head/keywords")),2,"Il numero di keywords non corrisponde!!");

        $r->set("/html/head/description","Questa è una descrizione di pagina!!");
        
        $this->assertTrue($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords non e' stato trovato!!");
        $this->assertTrue($r->is_set("/html/head/description"),"Il nodo /html/head/description non e' stato trovato!!");
        
        $r->remove("/html/head/keywords");
        $this->assertFalse($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords e' stato trovato!!");
        $this->assertTrue($r->is_set("/html/head/description"),"Il nodo /html/head/description non e' stato trovato!!");
        
        $r->remove("/html/head/description");
        $this->assertFalse($r->is_set("/html/head/description"),"Il nodo /html/head/description e' stato trovato!!");
        $this->assertTrue($r->is_set("/html/head"),"Il nodo /html/head non e' stato trovato!!");
        $this->assertTrue($r->is_set("/html"),"Il nodo /html non e' stato trovato!!");
    }
    
    function testClear()
    {        
        $r = new LHashMap();
        
        $r->set("/html/head/keywords",array("hello","spank"));
        
        $this->assertEqual(count($r->get("/html/head/keywords")),2,"Il numero di keywords non corrisponde!!");
        
        $this->assertTrue($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords non e' stato trovato!!");
        $this->assertTrue($r->is_set("/html/head"),"Il nodo /html/head non e' stato trovato!!");
        $this->assertTrue($r->is_set("/html"),"Il nodo /html non e' stato trovato!!");
        
        $r->clear();
        
        $this->assertFalse($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords e' stato trovato!!");
        $this->assertFalse($r->is_set("/html/head"),"Il nodo /html/head e' stato trovato!!");
        $this->assertFalse($r->is_set("/html"),"Il nodo /html e' stato trovato!!");
    }
    
    function testTreeSetGetAdd()
    {        
        $r = new LHashMap();
        
        $r->set("/html/head/keywords",array("hello","spank"));
        
        $this->assertEqual(count($r->get("/html/head/keywords")),2,"Il numero di keywords non corrisponde!!");
        
        $r->add("/html/head/keywords","ciccia");
        
        $this->assertEqual(count($r->get("/html/head/keywords")),3,"Il numero di keywords non corrisponde!!");
        
    }
    
    function testMerge()
    {        
        $r = new LHashMap();
        
        $r->set("/html/head/keywords",array("hello","spank"));
        
        $r->merge("/html/head/keywords",array("ciao","mondo"));
        
        $this->assertEqual(count($r->get("/html/head/keywords")),4,"Il numero di keywords non corrisponde!!");
    }
    
    function testPurge()
    {       
        $r = new LHashMap();
        
        $r->set("/html/head/keywords",array("hello","spank","blabla"));
        
        $this->assertEqual(count($r->get("/html/head/keywords")),3,"Il numero di keywords non corrisponde!!");
       
        $r->purge("/html/head/keywords",array("spank","blabla"));
        
        $this->assertEqual(count($r->get("/html/head/keywords")),1,"Il numero di keywords non corrisponde!!");

    }
    
    function testTreeHasNode()
    {       
        $r = new LHashMap();
        
        $this->assertFalse($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords e' stato trovato!!");
        
        $r->set("/html/head/keywords",array("hello","spank"));
        
        $this->assertTrue($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords non e' stato trovato!!");
        
        $r->remove("/html/head/keywords");
        
        $this->assertFalse($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords e' stato trovato!!");
        
    }
    
    
    function testGetChangeData()
    {
        $r = new LHashMap();
        
        $this->assertFalse($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords e' stato trovato!!");
        
        $r->set("/html/head/keywords",array("hello","spank"));
        
        $this->assertTrue($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords non e' stato trovato!!");
        $this->assertEqual(count($r->get("/html/head/keywords")),2,"Il numero di keywords non corrisponde!!");
        
    }
    
    function testComposedTrees()
    {
        $t1 = new LHashMap();
        $t1->set("/master/test",array("a","b","c","d"));
        $t1->set("/master/some_other_values","blahblahblah");
        
        $t2 = new LHashMap();
        $t2->set("/intresting/new_values",array("e","f","g","h","i"));
        
        $t1->set("/master/some_other_values",$t2);
        
        $this->assertEqual(count($t1->get("/master/some_other_values/intresting/new_values")),5,"Il numero dei valori nel branch non corrisponde!!");

    }




    function testDataEntry()
    {
        $t1 = new LHashMap();
        $t1->set("/master/test","xyz");

        $this->assertEqual($t1->get("/master/test"),"xyz","Il valore di /master/test non e' xyz!!");

        $t1->merge("/master/first_test",array("first" => "my_first_value"));
        $t1->merge("/master/second_test",array("second" => "my_second_value"));

        $this->assertEqual($t1->get("/master/first_test/first"),"my_first_value","Il valore di test non corrisponde!!");
        $this->assertEqual($t1->get("/master/second_test/second"),"my_second_value","Il valore di test non corrisponde!!");

    }
    
    
}
