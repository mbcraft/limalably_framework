<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class SelectStatementTest extends LTestCase {
	
	function testBasicSelect() {
		
		$db = db("framework_unit_tests");

		$s = select('*','mia_tabella');

		$this->assertEqual($s,"SELECT * FROM mia_tabella","L'SQL della select non corrisponde a quello atteso!");

	}

}