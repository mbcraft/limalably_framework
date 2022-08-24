<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class AndOrTest extends LTestCase {
	

	function testAnd() {

		$a1 = new LMysqlAndBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2',34));

		$this->assertEqual($a1,"( column_name1 = 12 AND column_name2 != 34 )","Il valore del blocco della AND non corrisponde a quello atteso!");

	}


	function testOr() {

		$a1 = new LMysqlOrBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2',34));

		$this->assertEqual($a1,"( column_name1 = 12 OR column_name2 != 34 )","Il valore del blocco della AND non corrisponde a quello atteso!");

	}


}