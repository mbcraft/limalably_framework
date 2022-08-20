<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class ConditionBlocksTest extends LTestCase {
		

	function testWhereBlock() {

		db('framework_unit_tests');

		$w1 = new LMysqlWhereBlock(LMysqlCondition::equal('field_name',12));
		$w2 = new LMysqlWhereBlock(new LMysqlAndBlock(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));
		$w3 = new LMysqlWhereBlock(new LMysqlOrBlock(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));
		$w4 = new LMysqlWhereBlock([LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')]);
		$w5 = new LMysqlWhereBlock(new LMysqlElementList(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));


		$this->assertEqual($w1," WHERE field_name = 12","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($w2," WHERE ( field_name1 = 12 AND field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($w3," WHERE ( field_name1 = 12 OR field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($w4," WHERE ( field_name1 = 12 AND field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($w5," WHERE ( field_name1 = 12 AND field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		

	}
}