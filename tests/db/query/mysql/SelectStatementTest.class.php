<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class SelectStatementTest extends LTestCase {
	
	function testBasicSelect() {
		
		$db = db("framework_unit_tests");

		$s1 = select('*','mia_tabella');
		$s2 = select('*','mia_tabella',_eq('a',1));
		$s3 = select('*','mia_tabella')->where(_eq('a',1));
		$s4 = select('*','mia_tabella')->left_join('join_tab',_eq('c',2))->where(_eq('a',1));
		
		$this->assertEqual($s1,"SELECT * FROM mia_tabella","L'SQL della select non corrisponde a quello atteso!");
		$this->assertEqual($s2,"SELECT * FROM mia_tabella WHERE a = 1","L'SQL della select non corrisponde a quello atteso!");
		$this->assertEqual($s3,"SELECT * FROM mia_tabella WHERE a = 1","L'SQL della select non corrisponde a quello atteso!");
		$this->assertEqual($s4,"SELECT * FROM mia_tabella LEFT JOIN join_tab ON c = 2 WHERE a = 1","L'SQL della select non corrisponde a quello atteso!");

	}

}