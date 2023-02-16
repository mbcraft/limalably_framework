<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class HostTest extends LTestCase {
	

	function testHostLocal() {

		if (LEnvironmentUtils::getRequestMethod()=='CLI') {

			$this->assertTrue(LHost::isLocal(),"L'host non è locale quando si lanciano gli unit test!");
			$this->assertFalse(LHost::isRemote(),"L'host è remoto quando si lanciano gli unit test!");

		}
	}

}