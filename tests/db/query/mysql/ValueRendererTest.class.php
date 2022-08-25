<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class ValueRendererTest extends LTestCase {
	

	function testValueRenderer() {

		db('framework_unit_tests');

		$v1 = new LMysqlValueRenderer('a');
		$v2 = new LMysqlValueRenderer(12);
		//$v3 = new LMysqlValueRenderer(new LMysqlValuePlaceholder());
		$v4 = new LMysqlValueRenderer(null);

		$this->assertEqual($v1,"'a'","Il valore del value renderer non corrisponde a quello atteso!");
		$this->assertEqual($v2,"12","Il valore del value renderer non corrisponde a quello atteso!");
		//$this->assertEqual($v3,"?","Il valore del value renderer non corrisponde a quello atteso!");
		$this->assertEqual($v4,"NULL","Il valore del value renderer non corrisponde a quello atteso!");
	}

}