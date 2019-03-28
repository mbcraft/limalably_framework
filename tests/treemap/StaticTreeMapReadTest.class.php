<?php

class StaticTreeMapReadTest extends LTestCase {
        
    function testBasicConfigReadData() {
        
        $value = LConfig::keys('/defaults/execution_mode');
        
        $this->assertTrue(is_array($value),"Il valore ritornato non è un array!");
        $this->assertEqual(count($value),5,"Il numero di chiavi non corrisponde!");
        
        $value2 = LConfig::mustGet('/defaults/logging/distinct-file/min_level');
        
        $this->assertEqual($value2,'debug',"Il valore letto dalla configurazione non corrisponde!");
        
    }
}
