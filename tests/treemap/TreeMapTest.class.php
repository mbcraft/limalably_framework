<?php

class TreeMapTest extends LTestCase {
    
    function testMergeWithArray() {
        
        $data = [42,18];
        
        $t = new LTreeMap();
        
        $t->set('/something/ancora',10);
        
        $t->merge('/', $data);
        
        $this->assertTrue($t->is_set(0),"Il valore atteso non è impostato!");
        $this->assertEqual($t->get(0),42,"Il valore atteso non corrisponde!");
        $this->assertTrue($t->is_set(1),"Il valore atteso non è impostato!");
        $this->assertEqual($t->get(1),18,"Il valore atteso non corrisponde!");
        
    }
    
    function testReplaceWithArray() {
        
        $data = [42,90];
        
        $t = new LTreeMap();
        
        $t->set('/something/ancora',10);
        
        $t->replace('/', $data);
        
        $this->assertTrue($t->is_set(0),"Il valore atteso non è impostato!");
        $this->assertEqual($t->get(0),42,"Il valore atteso non corrisponde!");
        $this->assertTrue($t->is_set(1),"Il valore atteso non è impostato!");
        $this->assertEqual($t->get(1),90,"Il valore atteso non corrisponde!");
        
    }
    
    function testReadingIndexedValuesFromTreeMap() {
        
        $indexed_values = ["ciao","mondo","caffè"];
        
        $t = new LTreeMap($indexed_values);
        
        $this->assertEqual($t->get(0),"ciao","Il valore letto non corrisponde!");
        $this->assertEqual($t->get("0"),"ciao","Il valore letto non corrisponde!");
        
        $this->assertEqual($t->get(1),"mondo","Il valore letto non corrisponde!");
        $this->assertEqual($t->get("1"),"mondo","Il valore letto non corrisponde!");
        
        $this->assertEqual($t->get(2),"caffè","Il valore letto non corrisponde!");
        $this->assertEqual($t->get("2"),"caffè","Il valore letto non corrisponde!");
        
        
    }
    
    function testReadingStrangeValuesFromTreeMap() {
        
        $t = new LTreeMap();
        $t->set('/uno/due/nullo',null);
        $t->set('/uno/due/false',false);
        $t->set('/uno/due/true',true);
        $t->set('/uno/due/zero',0);
        $t->set('/uno/due/uno',1);
        $t->set('/uno/due/meno_uno',-1);
        
        $this->assertTrue($t->is_set('/uno/due/nullo'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/false'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/true'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/zero'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/uno'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/meno_uno'),'Il nodo sembra non esistere!');
        
        $this->assertEqual($t->mustGet('/uno/due/nullo'),null,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGet('/uno/due/false'),false,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGet('/uno/due/true'),true,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGet('/uno/due/zero'),0,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGet('/uno/due/uno'),1,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGet('/uno/due/meno_uno'),-1,'Il nodo sembra non esistere!');
        
        $this->assertSame($t->mustGet('/uno/due/nullo'),null,'Il nodo ha un valore che non coincide! : '.var_export($t->mustGet('/uno/due/nullo'),true));
        $this->assertSame($t->mustGet('/uno/due/false'),false,'Il nodo ha un valore che non coincide!');
        $this->assertSame($t->mustGet('/uno/due/true'),true,'Il nodo ha un valore che non coincide!');
        $this->assertSame($t->mustGet('/uno/due/zero'),0,'Il nodo ha un valore che non coincide! : '.var_export($t->mustGet('/uno/due/zero'),true));
        $this->assertSame($t->mustGet('/uno/due/uno'),1,'Il nodo ha un valore che non coincide! : '.var_export($t->mustGet('/uno/due/uno'),true));
        $this->assertSame($t->mustGet('/uno/due/meno_uno'),-1,'Il nodo ha un valore che non coincide! : '.var_export($t->mustGet('/uno/due/meno_uno'),true));
        
    }
    
    function testReadingStrangeValuesFromTreeMap2() {
        
        $t = new LTreeMap();
        $t->set('/uno/due/nullo',null);
        $t->set('/uno/due/false',false);
        $t->set('/uno/due/true',true);
        $t->set('/uno/due/zero',0);
        $t->set('/uno/due/uno',1);
        $t->set('/uno/due/meno_uno',-1);
        
        $this->assertTrue($t->is_set('/uno/due/nullo'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/false'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/true'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/zero'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/uno'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/meno_uno'),'Il nodo sembra non esistere!');
        
        $this->assertEqual($t->mustGet('/uno/due')['nullo'],null,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGet('/uno/due')['false'],false,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGet('/uno/due')['true'],true,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGet('/uno/due')['zero'],0,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGet('/uno/due')['uno'],1,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGet('/uno/due')['meno_uno'],-1,'Il nodo sembra non esistere!');
        
        $this->assertSame($t->mustGet('/uno/due')['nullo'],null,'Il nodo nullo ha un valore che non coincide! : '.var_export($t->mustGet('/uno/due'),true));
        $this->assertSame($t->mustGet('/uno/due')['false'],false,'Il nodo falso ha un valore che non coincide!');
        $this->assertSame($t->mustGet('/uno/due')['true'],true,'Il nodo vero ha un valore che non coincide!');
        $this->assertSame($t->mustGet('/uno/due')['zero'],0,'Il nodo vero ha un valore che non coincide!');
        $this->assertSame($t->mustGet('/uno/due')['uno'],1,'Il nodo vero ha un valore che non coincide!');
        $this->assertSame($t->mustGet('/uno/due')['meno_uno'],-1,'Il nodo vero ha un valore che non coincide!');
        
    }
    
    function testReadingStrangeValuesFromTreeMap3() {
        
        $t = new LTreeMap();
        $t->set('/uno/due/nullo',null);
        $t->set('/uno/due/false',false);
        $t->set('/uno/due/true',true);
        $t->set('/uno/due/zero',0);
        $t->set('/uno/due/uno',1);
        $t->set('/uno/due/meno_uno',-1);
        
        $this->assertTrue($t->is_set('/uno/due/nullo'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/false'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/true'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/zero'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/uno'),'Il nodo sembra non esistere!');
        $this->assertTrue($t->is_set('/uno/due/meno_uno'),'Il nodo sembra non esistere!');
        
        $this->assertEqual($t->mustGetOriginal('/uno/due/nullo'),null,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGetOriginal('/uno/due/false'),false,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGetOriginal('/uno/due/true'),true,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGetOriginal('/uno/due/zero'),0,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGetOriginal('/uno/due/uno'),1,'Il nodo sembra non esistere!');
        $this->assertEqual($t->mustGetOriginal('/uno/due/meno_uno'),-1,'Il nodo sembra non esistere!');
        
        $this->assertSame($t->mustGetOriginal('/uno/due/nullo'),null,'Il nodo ha un valore che non coincide! : '.var_export($t->mustGetOriginal('/uno/due/nullo'),true));
        $this->assertSame($t->mustGetOriginal('/uno/due/false'),false,'Il nodo ha un valore che non coincide!');
        $this->assertSame($t->mustGetOriginal('/uno/due/true'),true,'Il nodo ha un valore che non coincide!');
        $this->assertSame($t->mustGetOriginal('/uno/due/zero'),0,'Il nodo ha un valore che non coincide! : '.var_export($t->mustGetOriginal('/uno/due/zero'),true));
        $this->assertSame($t->mustGetOriginal('/uno/due/uno'),1,'Il nodo ha un valore che non coincide! : '.var_export($t->mustGetOriginal('/uno/due/uno'),true));
        $this->assertSame($t->mustGetOriginal('/uno/due/meno_uno'),-1,'Il nodo ha un valore che non coincide! : '.var_export($t->mustGetOriginal('/uno/due/meno_uno'),true));
        
    }
    
    function testArrayNullValueIsSet() {
        $data = ["mykey" => null];
        
        $this->assertFalse(isset($data["mykey"]),"Il valore non è considerato valido per isset quando è salvato come null!");
        
        $this->assertTrue(array_key_exists("mykey", $data),"Il valore non è considerato valido anche con l'uso di array_key_exists!");
        
    }
    
    function testSetNullValueIsSet() {
        
        $map = new LTreeMap();
        
        $map->set("/some/path", null);
        
        $this->assertTrue($map->is_set('/some/path'),"Il valore non è considerato impostato quando è salvato come null!");
        
    }
    
    function testPathTokensFunctions1()
    {
        $path = "/html/head/keywords";
        
        $path_tokens = LTreeMap::path_tokens($path);

        $this->assertEqual(count($path_tokens),3,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token del path non corrisponde");
        $this->assertEqual($path_tokens[1],"head","Il token del path non corrisponde");
        $this->assertEqual($path_tokens[2],"keywords","Il token del path non corrisponde");
        
        $last_token = LTreeMap::last_path_token($path);
        $this->assertEqual($last_token,"keywords","Il token del path non corrisponde");
        
        $all_but_last = LTreeMap::all_but_last_path_tokens($path);

        $this->assertEqual(count($all_but_last),2,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($all_but_last[0],"html","Il token del path non corrisponde");
        $this->assertEqual($all_but_last[1],"head","Il token del path non corrisponde");
        
    }
    
    function testPathTokensFunctions2()
    {
        $path = "/html";
        
       
        $path_tokens = LTreeMap::path_tokens($path);

        $this->assertEqual(count($path_tokens),1,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token del path non corrisponde");
        
        $last_token = LTreeMap::last_path_token($path);
        $this->assertEqual($last_token,"html","Il token del path non corrisponde");
        
        $all_but_last = LTreeMap::all_but_last_path_tokens($path);
 
        $this->assertEqual(count($all_but_last),0,"Il numero dei path token non corrisponde!!");

    }
    
    function testPathTokensFunctions3()
    {
        $path = "/html//";
        
        $path_tokens = LTreeMap::path_tokens($path);

        $this->assertEqual(count($path_tokens),1,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token del path non corrisponde");
        
        $last_token = LTreeMap::last_path_token($path);
        $this->assertEqual($last_token,"html","Il token del path non corrisponde");
        
        $all_but_last = LTreeMap::all_but_last_path_tokens($path);
 
        $this->assertEqual(count($all_but_last),0,"Il numero dei path token non corrisponde!!");

    }
    
    function testPathTokensFunctions4()
    {
        $path = "//html///head/keywords/";
        
        $path_tokens = LTreeMap::path_tokens($path);

        $this->assertEqual(count($path_tokens),3,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($path_tokens[0],"html","Il token del path non corrisponde");
        $this->assertEqual($path_tokens[1],"head","Il token del path non corrisponde");
        $this->assertEqual($path_tokens[2],"keywords","Il token del path non corrisponde");
        
        $last_token = LTreeMap::last_path_token($path);
        $this->assertEqual($last_token,"keywords","Il token del path non corrisponde");
        
        $all_but_last = LTreeMap::all_but_last_path_tokens($path);

        $this->assertEqual(count($all_but_last),2,"Il numero dei path token non corrisponde!!");
        $this->assertEqual($all_but_last[0],"html","Il token del path non corrisponde");
        $this->assertEqual($all_but_last[1],"head","Il token del path non corrisponde");
        
    }
    
    function testSimpleLevel1()
    {
        $r = new LTreeMap();
        
        $r->set("/first/again","my_value");
        
        $this->assertTrue($r->is_set("/first/again"),"Il nodo first non e' stato creato!!");
        
        $this->assertEqual($r->get("/first/again"),"my_value","Il valore impostato non corrisponde!!");
        
        $r->clear();
        
        $this->assertFalse($r->is_set("/first/again"),"Il nodo first e' stato trovato!!");
        
    }
    
    function testAddAfterSet() {
        $r = new LTreeMap();
        
        $r->set("/html/head/keywords","hello");
        
        $this->assertFalse(is_array($r->get("/html/head/keywords")),"Il dato è un array e non dovrebbe esserlo!!");
        
        $r->add("/html/head/keywords","spank");
        
        $this->assertEqual(count($r->get("/html/head/keywords")),2,"Il numero di keywords non corrisponde!!");
        $this->assertEqual($r->get("/html/head/keywords")[0],"hello","Il numero di keywords non corrisponde!!");
        $this->assertEqual($r->get("/html/head/keywords")[1],"spank","Il numero di keywords non corrisponde!!");
    }
    
    function testAdd()
    {
        $r = new LTreeMap();
        
        $r->add("/html/head/keywords","hello");
        
        $this->assertEqual(count($r->get("/html/head/keywords")),1,"Il numero di keywords non corrisponde!!");
        
        $r->add("/html/head/keywords","spank");
        
        $this->assertEqual(count($r->get("/html/head/keywords")),2,"Il numero di keywords non corrisponde!!");
    }
    
    function testRemove()
    {
        $r = new LTreeMap();
        
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
        $r = new LTreeMap();
        
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
        $r = new LTreeMap();
        
        $r->set("/html/head/keywords",array("hello","spank"));
        
        $this->assertEqual(count($r->get("/html/head/keywords")),2,"Il numero di keywords non corrisponde!!");
        
        $r->add("/html/head/keywords","ciccia");
        
        $this->assertEqual(count($r->get("/html/head/keywords")),3,"Il numero di keywords non corrisponde!!");
        
    }
    
    function testMerge()
    {        
        $r = new LTreeMap();
        
        $r->set("/html/head/keywords",array("hello","spank"));
        
        $r->merge("/html/head/keywords",array("ciao","mondo"));
        
        $this->assertEqual(count($r->get("/html/head/keywords")),4,"Il numero di keywords non corrisponde!!");
    }
    
    function testPurge()
    {       
        $r = new LTreeMap();
        
        $r->set("/html/head/keywords",array("hello","spank","blabla"));
        
        $this->assertEqual(count($r->get("/html/head/keywords")),3,"Il numero di keywords non corrisponde!!");
       
        $r->purge("/html/head/keywords",array("spank","blabla"));
        
        $this->assertEqual(count($r->get("/html/head/keywords")),1,"Il numero di keywords non corrisponde!!");

    }
    
    function testTreeHasNode()
    {       
        $r = new LTreeMap();
        
        $this->assertFalse($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords e' stato trovato!!");
        
        $r->set("/html/head/keywords",array("hello","spank"));
        
        $this->assertTrue($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords non e' stato trovato!!");
        
        $r->remove("/html/head/keywords");
        
        $this->assertFalse($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords e' stato trovato!!");
        
    }
    
    function testGetChangeData()
    {
        $r = new LTreeMap();
        
        $this->assertFalse($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords e' stato trovato!!");
        
        $r->set("/html/head/keywords",array("hello","spank"));
        
        $this->assertTrue($r->is_set("/html/head/keywords"),"Il nodo /html/head/keywords non e' stato trovato!!");
        $this->assertEqual(count($r->get("/html/head/keywords")),2,"Il numero di keywords non corrisponde!!");
       
        
        $html = $r->view("/html");
        
        $html->set("head/keywords",array("pippo","pluto","paperino"));
        
        $this->assertEqual(count($html->get("head/keywords")),3,"Il numero di keywords non corrisponde!!");
        
        $this->assertEqual(count($r->get("/html/head/keywords")),3,"Il numero di keywords non corrisponde!!");
        
    }
    
    function testBidirectionalView()
    {
        $t1 = new LTreeMap();
        
        $t1->set("/prova","ciao");
        $t1->set("/altro/prove/valori",array("primo","secondo","terzo"));
        
        $t2 = $t1->view("/altro/prove");
        
        $this->assertEqual(count($t1->get("/altro/prove/valori")),3,"il numero dei valori non corrisponde!!");
        $this->assertEqual(count($t2->get("valori",[])),3,"il numero dei valori non corrisponde!!");
    
        $t1->add("/altro/prove/valori","quarto");
        $this->assertEqual(count($t1->get("/altro/prove/valori")),4,"il numero dei valori non corrisponde!!");
        $this->assertEqual(count($t2->get("valori")),4,"il numero dei valori non corrisponde!!");
    
        $t1->add("/altro/prove/valori","quinto");
        $this->assertEqual(count($t1->get("/altro/prove/valori")),5,"il numero dei valori non corrisponde!!");
        $this->assertEqual(count($t2->get("valori")),5,"il numero dei valori non corrisponde!!");
     
    }
    
    function testComposedTrees()
    {
        $t1 = new LTreeMap();
        $t1->set("/master/test",array("a","b","c","d"));
        $t1->set("/master/some_other_values","blahblahblah");
        
        $t2 = new LTreeMap();
        $t2->set("/intresting/new_values",array("e","f","g","h","i"));
        
        $t1->set("/master/some_other_values",$t2);
        
        $this->assertEqual(count($t1->get("/master/some_other_values/intresting/new_values")),5,"Il numero dei valori nel branch non corrisponde!!");

    }

    function testIterator1() {
        $t = new LTreeMap(["a" => 1,"b" => 2,"c" => 3]);
        
        $total = 0;
        foreach ($t as $k => $v) {
            $total += $v;
        }
        
        $this->assertEqual($total,6,"Il valore atteso non corrisponde!");
    }
    
    function testIterator2() {
        $t = new LTreeMap(["a" => 1,"b" => 2,"c" => ["aa" => 1,"bb" => 15]]);
        
        $tm = $t->view("c");
        $total = 0;
        foreach ($tm as $k => $v) {
            $total += $v;
        }
        
        $this->assertEqual($total,16,"Il valore atteso non corrisponde!");
    }



    function testDataEntry()
    {
        $t1 = new LTreeMap();
        $t1->set("/master/test","xyz");

        $this->assertEqual($t1->get("/master/test"),"xyz","Il valore di /master/test non e' xyz!!");

        $t1->merge("/master/first_test",array("first" => "my_first_value"));
        $t1->merge("/master/second_test",array("second" => "my_second_value"));

        $this->assertEqual($t1->get("/master/first_test/first"),"my_first_value","Il valore di test non corrisponde!!");
        $this->assertEqual($t1->get("/master/second_test/second"),"my_second_value","Il valore di test non corrisponde!!");

    }
    
    
}
