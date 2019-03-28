<?php

class StaticTreeMapWriteTest extends LTestCase {

    use LStaticTreeMapBase;
    use LStaticReadTreeMap;
    use LStaticWriteTreeMap;

    function testFalseStrings() {
        self::clear();
        
        self::set('/abc/a','false');
        self::set('/abc/b','0');
        self::set('/abc/c','no');
        
        $this->assertFalse(self::getBoolean('/abc/a'),"Il booleano non è corretto");
        $this->assertFalse(self::getBoolean('/abc/b'),"Il booleano non è corretto");
        $this->assertFalse(self::getBoolean('/abc/c'),"Il booleano non è corretto");
    }

    function testSimpleLevel1() {
        self::clear();

        self::set("/first", "my_value");

        $this->assertTrue(self::is_set("/first"), "Il nodo first non e' stato creato!!");

        $this->assertEqual(self::get("/first"), "my_value", "Il valore impostato non corrisponde!!");

        self::clear();

        $this->assertFalse(self::is_set("/first"), "Il nodo first e' stato trovato!!");
    }

    function testAdd() {
        self::clear();

        self::add("/html/head/keywords", "hello");

        $this->assertEqual(count(self::get("/html/head/keywords")), 1, "Il numero di keywords non corrisponde!!");

        self::add("/html/head/keywords", "spank");

        $this->assertEqual(count(self::get("/html/head/keywords")), 2, "Il numero di keywords non corrisponde!!");
    }

    function testRemove() {
        self::clear();

        self::set("/html/head/keywords", array("hello", "world"));

        $this->assertEqual(count(self::get("/html/head/keywords")), 2, "Il numero di keywords non corrisponde!!");

        self::set("/html/head/description", "Questa è una descrizione di pagina!!");

        $this->assertTrue(self::is_set("/html/head/keywords"), "Il nodo /html/head/keywords non e' stato trovato!!");
        $this->assertTrue(self::is_set("/html/head/description"), "Il nodo /html/head/description non e' stato trovato!!");

        self::remove("/html/head/keywords");
        $this->assertFalse(self::is_set("/html/head/keywords"), "Il nodo /html/head/keywords e' stato trovato!!");
        $this->assertTrue(self::is_set("/html/head/description"), "Il nodo /html/head/description non e' stato trovato!!");

        self::remove("/html/head/description");
        $this->assertFalse(self::is_set("/html/head/description"), "Il nodo /html/head/description e' stato trovato!!");
        $this->assertTrue(self::is_set("/html/head"), "Il nodo /html/head non e' stato trovato!!");
        $this->assertTrue(self::is_set("/html"), "Il nodo /html non e' stato trovato!!");
    }

    function testClear() {
        self::clear();

        self::set("/html/head/keywords", array("hello", "spank"));

        $this->assertEqual(count(self::get("/html/head/keywords")), 2, "Il numero di keywords non corrisponde!!");

        $this->assertTrue(self::is_set("/html/head/keywords"), "Il nodo /html/head/keywords non e' stato trovato!!");
        $this->assertTrue(self::is_set("/html/head"), "Il nodo /html/head non e' stato trovato!!");
        $this->assertTrue(self::is_set("/html"), "Il nodo /html non e' stato trovato!!");

        self::clear();

        $this->assertFalse(self::is_set("/html/head/keywords"), "Il nodo /html/head/keywords e' stato trovato!!");
        $this->assertFalse(self::is_set("/html/head"), "Il nodo /html/head e' stato trovato!!");
        $this->assertFalse(self::is_set("/html"), "Il nodo /html e' stato trovato!!");
    }

    function testTreeSetGetAdd() {
        self::clear();

        self::set("/html/head/keywords", array("hello", "spank"));

        $this->assertEqual(count(self::get("/html/head/keywords")), 2, "Il numero di keywords non corrisponde!!");

        self::add("/html/head/keywords", "ciccia");

        $this->assertEqual(count(self::get("/html/head/keywords")), 3, "Il numero di keywords non corrisponde!!");
    }

    function testMerge() {
        self::clear();

        self::set("/html/head/keywords", array("hello", "spank"));

        self::merge("/html/head/keywords", array("ciao", "mondo"));

        $this->assertEqual(count(self::get("/html/head/keywords")), 4, "Il numero di keywords non corrisponde!!");
    }

    function testPurge() {
        self::clear();

        self::set("/html/head/keywords", array("hello", "spank", "blabla"));

        $this->assertEqual(count(self::get("/html/head/keywords")), 3, "Il numero di keywords non corrisponde!!");

        self::purge("/html/head/keywords", array("spank", "blabla"));

        $this->assertEqual(count(self::get("/html/head/keywords")), 1, "Il numero di keywords non corrisponde!!");
    }

    function testTreeHasNode() {
        self::clear();

        $this->assertFalse(self::is_set("/html/head/keywords"), "Il nodo /html/head/keywords e' stato trovato!!");

        self::set("/html/head/keywords", array("hello", "spank"));

        $this->assertTrue(self::is_set("/html/head/keywords"), "Il nodo /html/head/keywords non e' stato trovato!!");

        self::remove("/html/head/keywords");

        $this->assertFalse(self::is_set("/html/head/keywords"), "Il nodo /html/head/keywords e' stato trovato!!");
    }

    function testGetChangeData() {
        self::clear();

        $this->assertFalse(self::is_set("/html/head/keywords"), "Il nodo /html/head/keywords e' stato trovato!!");

        self::set("/html/head/keywords", array("hello", "spank"));

        $this->assertTrue(self::is_set("/html/head/keywords"), "Il nodo /html/head/keywords non e' stato trovato!!");
        $this->assertEqual(count(self::get("/html/head/keywords")), 2, "Il numero di keywords non corrisponde!!");


    }

    function testDataEntry() {
        self::clear();
        self::set("/master/test", "xyz");

        $this->assertEqual(self::get("/master/test"), "xyz", "Il valore di /master/test non e' xyz!!");

        self::merge("/master/first_test", array("first" => "my_first_value"));
        self::merge("/master/second_test", array("second" => "my_second_value"));

        $this->assertEqual(self::get("/master/first_test/first"), "my_first_value", "Il valore di test non corrisponde!!");
        $this->assertEqual(self::get("/master/second_test/second"), "my_second_value", "Il valore di test non corrisponde!!");
    }

}
