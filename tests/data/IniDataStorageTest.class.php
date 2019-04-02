<?php

class IniDataStorageTest extends LTestCase {
    
    function testGetData() {
        
        $d = new LIniDataStorage();
        $d->init($_SERVER['FRAMEWORK_DIR'].'tests/');
        
        $this->assertTrue($d->is_saved('data/my_data2'),"Il file con i dati non è stato trovato!");
        
        $my_data = $d->load('data/my_data2');
                
        $this->assertEqual($my_data['key']['something'],"value","Il valore letto dall'ini non corrisponde! : ".$my_data['key']['something']);
        $this->assertEqual($my_data['qualcosa']['ancora'],32,"Il valore letto dall'ini non corrisponde!");
        $this->assertTrue($my_data['qualcosa']['dai'],"Il valore letto dall'ini non corrisponde!");
        $this->assertNull($my_data['qualcosa']['nullo'],"Il valore letto dall'ini non corrisponde!");
        
        
    }
    
}
