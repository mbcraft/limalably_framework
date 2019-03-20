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

    
}
