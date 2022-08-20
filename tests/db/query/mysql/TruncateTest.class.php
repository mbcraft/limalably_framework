<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class TruncateTest extends LTestCase {
	

	function testTruncate()
	{

		db('framework_unit_tests');

		$t = truncate('table_name123');

		$this->assertEqual($t,"TRUNCATE TABLE table_name123","Il codice SQL della truncate non corrisponde a quello atteso!");

	}

}