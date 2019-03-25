<?php

class MiscTest extends LTestCase {
    
    function testDirname0() {
        
        $my_file = $_SERVER['FRAMEWORK_DIR'].'logs/miofile.txt';
        
        $my_dirname_dir = dirname($my_file);
        
        $this->assertEqual($my_dirname_dir,$_SERVER['FRAMEWORK_DIR'].'logs',"Le directory non sono come quelle attese! : ".$my_dirname_dir);
        
    }
    
    
    function testDirname1() {
        
        $my_dir = $_SERVER['FRAMEWORK_DIR'].'logs/';
        
        $my_dirname_dir = dirname($my_dir);
        
        $this->assertEqual($my_dirname_dir.'/',$_SERVER['FRAMEWORK_DIR'],"Le directory non sono come quelle attese! : ".$my_dirname_dir);
        
    }

    function testDirname2() {
        
        $my_file = $_SERVER['FRAMEWORK_DIR'].'miofile.txt';
        
        $my_dirname_dir = dirname($my_file);
        
        $this->assertEqual($my_dirname_dir.'/',$_SERVER['FRAMEWORK_DIR'],"Le directory non sono come quelle attese! : ".$my_dirname_dir);
        
    }
    
    function testArrayMergeRecursive() {
        
        $data1 = array("color" => "red","apple","size" => array("A4"));
        $data2 = array("color" => "green","banana","size" => array("A3"));
        
        $result = array_merge_recursive($data1,$data2);
        
        $this->assertEqual(count($result["color"]),2,"Il numero di valori dell'array color non è valido!");
        $this->assertEqual(count($result["size"]),2,"Il numero di valori dell'array size non è valido!");
        
    }
    
    function testArrayReplaceRecursive() {
        $data1 = array("color" => "red","apple","size" => array("A4"));
        $data2 = array("color" => "green","banana","size" => array("A3"));
        
        $result = array_replace_recursive($data1,$data2);
        
        $this->assertEqual($result["color"],"green","Il risultato non è valido!");
        $this->assertEqual(count($result["size"]),1,"Il numero di valori dell'array size non è valido!");
    }

    
}
