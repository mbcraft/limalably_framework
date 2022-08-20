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

	function testHavingBlock() {

		db('framework_unit_tests');

		$h1 = new LMysqlHavingBlock(LMysqlCondition::equal('field_name',12));
		$h2 = new LMysqlHavingBlock(new LMysqlAndBlock(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));
		$h3 = new LMysqlHavingBlock(new LMysqlOrBlock(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));
		$h4 = new LMysqlHavingBlock([LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')]);
		$h5 = new LMysqlHavingBlock(new LMysqlElementList(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));


		$this->assertEqual($h1," HAVING field_name = 12","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($h2," HAVING ( field_name1 = 12 AND field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($h3," HAVING ( field_name1 = 12 OR field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($h4," HAVING ( field_name1 = 12 AND field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($h5," HAVING ( field_name1 = 12 AND field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		

	}

	function testOnBlock() {

		db('framework_unit_tests');

		$o1 = new LMysqlOnBlock(LMysqlCondition::equal('field_name',12));
		$o2 = new LMysqlOnBlock(new LMysqlAndBlock(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));
		$o3 = new LMysqlOnBlock(new LMysqlOrBlock(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));
		$o4 = new LMysqlOnBlock([LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')]);
		$o5 = new LMysqlOnBlock(new LMysqlElementList(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));


		$this->assertEqual($o1," ON field_name = 12","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($o2," ON ( field_name1 = 12 AND field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($o3," ON ( field_name1 = 12 OR field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($o4," ON ( field_name1 = 12 AND field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($o5," ON ( field_name1 = 12 AND field_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		

	}
}