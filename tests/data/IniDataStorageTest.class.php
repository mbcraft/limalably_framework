<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

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
    
    function testSaveDataWithIniStorage() {
        $d = new LIniDataStorage();
        $d->init($_SERVER['FRAMEWORK_DIR'].'tests/tmp/');

        $d->delete("prova");

        $data = ['a' => 1,'b' => 2,'c' => 'x'];

        $d->save('prova',$data);

        $this->assertTrue($d->isSaved('prova'),"I dati ini non sono stati salvati!");

        $d->delete('prova');

        $this->assertFalse($d->isSaved('prova'),"I dati ini non sono stati salvati!");

    }

    function testNestedKeys() {

        $d = new LIniDataStorage();
        $d->init($_SERVER['FRAMEWORK_DIR'].'tests/tmp/');
        
        $data = array('a' => 1,'b' => array('c' => 2,'d' => 3));

        $d->save('prova_chiavi_annidate',$data);

        $result = $d->load('prova_chiavi_annidate');

        $this->assertEqual($result['a'],1,"Il dato non è più presente nei risultati!");
        $this->assertEqual($result['b']['c'],2,"Il dato non è più presente nei risultati!");
        $this->assertEqual($result['b']['d'],3,"Il dato non è più presente nei risultati!");
           
    }

    function testGetDataFromRealIni() {
        $d = new LIniDataStorage();
        $d->init($_SERVER['FRAMEWORK_DIR'].'tests/');
        
        $this->assertTrue($d->isSaved('data/contacts'),"Il file con i dati non è stato trovato!");
        
        $my_data = $d->load('data/contacts');
                
        $this->assertEqual($my_data['contacts']['form']['phone']['label'],"Telefono","I dati letti non corrispondono! : ".$my_data['contacts']['form']['phone']['label']);
    }
    
}
