<?php

class IniDataStorageTest extends LTestCase {
    
    function testGetData() {
        
        $d = new LIniDataStorage();
        $d->init($_SERVER['FRAMEWORK_DIR'].'tests/');
        
        $this->assertTrue($d->isSaved('data/my_data2'),"Il file con i dati non è stato trovato!");
        
        $my_data = $d->load('data/my_data2');
                
        $this->assertEqual($my_data['key']['something'],"value","Il valore letto dall'ini non corrisponde! : ".$my_data['key']['something']);
        $this->assertEqual($my_data['qualcosa']['ancora'],32,"Il valore letto dall'ini non corrisponde!");
        $this->assertEqual($my_data['qualcosa']['dai'],"true","Il valore letto dall'ini non corrisponde!");
        $this->assertEqual($my_data['qualcosa']['nullo'],"null","Il valore letto dall'ini non corrisponde!");
        
        
    }
    
    function testGetDataFromRealIni() {
        $d = new LIniDataStorage();
        $d->init($_SERVER['FRAMEWORK_DIR'].'tests/');
        
        $this->assertTrue($d->isSaved('data/contacts'),"Il file con i dati non è stato trovato!");
        
        $my_data = $d->load('data/contacts');
                
        $this->assertEqual($my_data['contacts']['form']['phone']['label'],"Telefono","I dati letti non corrispondono! : ".$my_data['contacts']['form']['phone']['label']);
    }
    
}
