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
    
    function testDirname3() {
        $my_path = "qualcosa/qualcosaltro";
        
        $dirname = dirname($my_path);
        
        $this->assertEqual($dirname,'qualcosa',"Il dirname non corrisponde!");
        
        $dirname2 = dirname($dirname);
        
        $this->assertEqual($dirname2,'.',"Il dirname2 non corrisponde! : ".$dirname2);
        
        $dirname3 = dirname($dirname2);
        
        $this->assertEqual($dirname3,'.',"Il dirname3 non corrisponde! : ".$dirname3);
    }
    
    function testDirname4() {
        $my_path = "/qualcosa/qualcosaltro";
        
        $dirname = dirname($my_path);
        
        $this->assertEqual($dirname,'/qualcosa',"Il dirname non corrisponde!");
        
        $dirname2 = dirname($dirname);
        
        $this->assertEqual($dirname2,'/',"Il dirname2 non corrisponde! : ".$dirname2);
        
        $dirname3 = dirname($dirname2);
        
        $this->assertEqual($dirname3,'/',"Il dirname3 non corrisponde! : ".$dirname3);
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
    
    function testArrayReplaceRecursive2() {
        $data1 = array("color" => array("type" => "red"),"apple","size" => array("A4"));
        $data2 = array("color" => array("something" => "green"),"banana","size" => array("A3"));
        
        $result = array_replace_recursive($data1,$data2);
        
        $this->assertEqual(count($result["color"]),2,"Il risultato non è valido!");
        $this->assertEqual($result["color"]["type"],"red","Il risultato non è valido!");
        $this->assertEqual($result["color"]["something"],"green","Il risultato non è valido!");
        $this->assertEqual(count($result["size"]),1,"Il numero di valori dell'array size non è valido!");
    }
    

    
}
