<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class RouteCaptureTest extends LTestCase {
    
    function testCapture1() {
        
        $c = new LRouteCapture();
        
        $result = $c->captureParameters("*/<prova>/*.<id>.*", "cartella/fiore/ancora.13.html");
        $this->assertNotNull($result,"Il risultato è vuoto");
        $this->assertEqual($result['prova'],'fiore','Il parametro non è stato catturato correttamente!');
        $this->assertEqual($result['id'],13,'Il parametro non è stato catturato correttamente!');
    }
    
    function testCapture2() {
        
        $c = new LRouteCapture();
        
        $result = $c->captureParameters("*/<prova>/*.<id>.*", "cartella/fi+ore/ancora.13.html");
        $this->assertNotNull($result,"Il risultato è vuoto");
        $this->assertEqual($result['prova'],'fi+ore','Il parametro non è stato catturato correttamente!');
        $this->assertEqual($result['id'],13,'Il parametro non è stato catturato correttamente!');
    }
    
    function testCapture3() {
        
        $c = new LRouteCapture();
        
        $result = $c->captureParameters("*/<prova>/*.<id>.*", "cartella/fi-or_eE/an-co_ra.13.html");
        $this->assertNotNull($result,"Il risultato è vuoto");
        $this->assertEqual($result['prova'],'fi-or_eE','Il parametro non è stato catturato correttamente!');
        $this->assertEqual($result['id'],13,'Il parametro non è stato catturato correttamente!');
    }
    
    function testCapture4() {
        
        $c = new LRouteCapture();
        
        $result = $c->captureParameters("*/<prova>/*.<id>.*", "cartella/(fi-or_+eE!$)/an-co_!$+ra.13.html");
        $this->assertNotNull($result,"Il risultato è vuoto");
        $this->assertEqual($result['prova'],'(fi-or_+eE!$)','Il parametro non è stato catturato correttamente!');
        $this->assertEqual($result['id'],13,'Il parametro non è stato catturato correttamente!');
    }
    
}
