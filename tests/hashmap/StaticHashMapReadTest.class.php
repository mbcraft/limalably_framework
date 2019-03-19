<?php

class StaticHashMapReadTest extends LTestCase {
        
    function testBasicConfigReadData() {
        
        $value = LConfig::keys('/defaults/execution_mode');
        
        $this->assertTrue(is_array($value),"Il valore ritornato non è un array!");
        $this->assertEqual(count($value),5,"Il numero di chiavi non corrisponde!");
        
        $value2 = LConfig::mustGet('/defaults/routemap/hash_db/levels');
        
        $this->assertEqual($value2,5,"Il valore letto dalla configurazione non corrisponde!");
        
    }
}
