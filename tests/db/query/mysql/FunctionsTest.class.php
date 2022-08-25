<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class FunctionsTest extends LTestCase {
	

	function testIsNull() {

		db('framework_unit_tests');

		$result = _ifnull('my_column_name','ABC');

		$this->assertEqual($result,"IFNULL ( my_column_name , 'ABC' )","Il risultato della funzione mysql non coincide con quello atteso!");
	}


}