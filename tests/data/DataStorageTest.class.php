<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class DataStorageTest extends LTestCase {
    
    function testMixedDataStorage() {
        
        $d = new LDataStorage();
        $d->init($_SERVER['FRAMEWORK_DIR']);
        
        $this->assertTrue($d->isSaved("tests/data/repos/mixed_data"),"I dati salvati non sono stati trovati!");
        
        $my_data = $d->load("tests/data/repos/mixed_data");
        
        $this->assertTrue(isset($my_data['key']),"I dati non sono stati letti correttamente!");
        $this->assertTrue(isset($my_data['key']['mixed']['value']),"I dati non sono stati letti correttamente!");
        $this->assertTrue(isset($my_data['key']['mixed']['due']),"I dati non sono stati letti correttamente!");
        $this->assertTrue(isset($my_data['key']['mixed']['quattordici']),"I dati non sono stati letti correttamente!");
        
        $this->assertEqual($my_data['key']['mixed']['value'],"value","I dati non sono stati letti correttamente!");
        $this->assertEqual($my_data['key']['mixed']['due'],2,"I dati non sono stati letti correttamente!");
        $this->assertEqual($my_data['key']['mixed']['quattordici'],14,"I dati non sono stati letti correttamente!");
    }
    
    function testZipUnzipDataStorage() {

        $d = new LDataStorage();
        $d->init($_SERVER['FRAMEWORK_DIR'].'tests/data/tmp/');

        $zip_file = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/data/temp_save/storage_test.zip');
        if ($zip_file->exists()) $zip_file->delete();

        $s2 = new LDir($_SERVER['FRAMEWORK_DIR'].'tests/data/tmp2/');
        $s2->delete(true);

        $s2->touch();

        $d->zipStorageAs($zip_file);

        $this->assertTrue($zip_file->exists(),"Il file archivio non è stato creato!");

        $d2 = new LDataStorage();
        $d2->init($_SERVER['FRAMEWORK_DIR'].'tests/data/tmp2/');

        $d2->unzipAsStorage($zip_file);

        $my_data = $d2->load('prova');

        $this->assertEqual($my_data['c'],'x',"Lo storage non è stato gestito correttamente!");


    }
}
