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
    
}
