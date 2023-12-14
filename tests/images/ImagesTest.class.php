<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class ImagesTest extends LTestCase {
	
    function testGdAreSupported()
    {
        $this->assertTrue(function_exists("imagecreatetruecolor"),"La funzione imagecreatetruecolor non e' supportata. Installare le librerie GD per PHP!!");
    }
}