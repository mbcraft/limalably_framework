<?php

class DataStorageTest extends LTestCase {
    
    function testMixedDataStorage() {
        
        $d = new LDataStorage();
        $d->init($_SERVER['FRAMEWORK_DIR']);
        
        $this->assertTrue($d->is_saved("tests/data/mixed_data"),"I dati salvati non sono stati trovati!");
        
        $my_data = $d->load("tests/data/mixed_data");
        
        $this->assertTrue(isset($my_data['key']),"I dati non sono stati letti correttamente!");
        $this->assertTrue(isset($my_data['key']['mixed']['value']),"I dati non sono stati letti correttamente!");
        $this->assertTrue(isset($my_data['key']['mixed']['due']),"I dati non sono stati letti correttamente!");
        $this->assertTrue(isset($my_data['key']['mixed']['quattordici']),"I dati non sono stati letti correttamente!");
        
        $this->assertEqual($my_data['key']['mixed']['value'],"value","I dati non sono stati letti correttamente!");
        $this->assertEqual($my_data['key']['mixed']['due'],2,"I dati non sono stati letti correttamente!");
        $this->assertEqual($my_data['key']['mixed']['quattordici'],14,"I dati non sono stati letti correttamente!");
    }
    
}
