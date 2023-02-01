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
        
        $data = $storage->load('tests/data/repos/my_data');
        
        $this->assertEqual($data['ciao'],"mondo","Il dato letto non corrisponde!");
        $this->assertEqual($data['prova'],14,"Il dato letto non corrisponde!");
    }
    
    function testSetAndGet() {
        $final_path = $_SERVER['FRAMEWORK_DIR'].'tests/data/repos/my_saved_data.json';
        if (is_file($final_path)) @unlink($final_path);
        
        $my_data = ["qualcosa" => "qualcuno","ancora" => 18];
        
        $storage = new LJsonDataStorage();
        $storage->init($_SERVER['FRAMEWORK_DIR']);
        
        $storage->save('tests/data/repos/my_saved_data',$my_data);
        
        $my_data_again = $storage->load('tests/data/repos/my_saved_data');
        
        $this->assertEqual($my_data_again['qualcosa'],"qualcuno","I dati salvati non corrispondono!");
        $this->assertEqual($my_data_again['ancora'],18,"I dati salvati non corrispondono!");
        
        
    }

    private function dataHasNewlines(string $value) {
        return preg_match("/[\r\n]/",$value,$matches)!==0;
    }

    public function testCRLF() {

        $data1 = "abcd";

        $data2 = "ab\r\ncd";

        $data3 = "ab\rcd";

        $data4 = "ab\ncd";

        $this->assertFalse($this->dataHasNewlines($data1),"Il dato contiene andate a capo!!");
        $this->assertTrue($this->dataHasNewlines($data2),"Il dato non contiene andate a capo!!");
        $this->assertTrue($this->dataHasNewlines($data3),"Il dato non contiene andate a capo!!");
        $this->assertTrue($this->dataHasNewlines($data4),"Il dato non contiene andate a capo!!");



    }
    
}
