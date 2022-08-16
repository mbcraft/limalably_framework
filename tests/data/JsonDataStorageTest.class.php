<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class JsonDataStorageTest extends LTestCase {
    
    function testGet() {
        $storage = new LJsonDataStorage();
        $storage->init($_SERVER['FRAMEWORK_DIR']);
        
        $data = $storage->load('tests/data/my_data');
        
        $this->assertEqual($data['ciao'],"mondo","Il dato letto non corrisponde!");
        $this->assertEqual($data['prova'],14,"Il dato letto non corrisponde!");
    }
    
    function testSetAndGet() {
        $final_path = $_SERVER['FRAMEWORK_DIR'].'tests/data/my_saved_data.json';
        if (is_file($final_path)) @unlink($final_path);
        
        $my_data = ["qualcosa" => "qualcuno","ancora" => 18];
        
        $storage = new LJsonDataStorage();
        $storage->init($_SERVER['FRAMEWORK_DIR']);
        
        $storage->save('tests/data/my_saved_data',$my_data);
        
        $my_data_again = $storage->load('tests/data/my_saved_data');
        
        $this->assertEqual($my_data_again['qualcosa'],"qualcuno","I dati salvati non corrispondono!");
        $this->assertEqual($my_data_again['ancora'],18,"I dati salvati non corrispondono!");
        
        
    }
    
}
