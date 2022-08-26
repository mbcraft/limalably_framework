<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class XmlDataStorageTest extends LTestCase {
    
    function testGet() {
        
        $storage = new LXmlDataStorage();
        $storage->init($_SERVER['FRAMEWORK_DIR']);
        
        $data = $storage->load('tests/data/my_data3');
        
        $this->assertEqual($data['qualcosa']['uno'],42,"Il dato letto non corrisponde!");
        $this->assertEqual($data['qualcosa']['ancora']['due'],"Ciao","Il dato letto non corrisponde!");
        
        $ancora3 = $data['qualcosa']['ancora']['tre'];
        
        $this->assertTrue(LStringUtils::contains($ancora3, '<br>'), "L'html non è stato letto correttamente!");
        $this->assertTrue(LStringUtils::contains($ancora3, '<hr>'), "L'html non è stato letto correttamente!");
        $this->assertTrue(LStringUtils::contains($ancora3, 'Questo è un testo di prova'), "L'html non è stato letto correttamente!");
        $this->assertTrue(LStringUtils::contains($ancora3, '<div>'), "L'html non è stato letto correttamente!");
        $this->assertTrue(LStringUtils::contains($ancora3, '</div>'), "L'html non è stato letto correttamente!");
        
    }

    function testSaveLoadData() {
        $storage = new LXmlDataStorage();
        $storage->init($_SERVER['FRAMEWORK_DIR']);
        
        $storage->init('tests/tmp/');

        $test_html = <<<EOH

<span>Hello world</span>

EOH;

        $data = ['a' => 1,'b' => 2,'c' => 'x','d' => $test_html];

        $storage->delete('prova');

        $this->assertFalse($storage->isSaved('prova'),"I dati sono ancora presenti!");

        $storage->save('prova',$data);

        $this->assertTrue($storage->isSaved('prova'),"I dati sono ancora presenti!");

        $loaded_data = $storage->load('prova');

        $this->assertEqual($loaded_data['a'],1,"I dati letti non corrispondono!");
        $this->assertEqual($loaded_data['b'],2,"I dati letti non corrispondono!");
        $this->assertEqual($loaded_data['c'],'x',"I dati letti non corrispondono!");
        $this->assertEqual($loaded_data['d'],$test_html,"L'html letto dall'xml non corrisponde a quello salvato!");

        //$storage->delete('prova');
    }
    
}
