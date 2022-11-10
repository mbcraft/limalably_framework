<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MysqlConditionBlocksTest extends LTestCase {
		

	function testWhereBlock() {

		db('hosting_dreamhost_tests');

		$w1 = new LMysqlWhereBlock(LMysqlCondition::equal('column_name',12));
		$w2 = new LMysqlWhereBlock(new LMysqlAndBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$w3 = new LMysqlWhereBlock(new LMysqlOrBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$w4 = new LMysqlWhereBlock([LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')]);
		$w5 = new LMysqlWhereBlock(new LMysqlElementList(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));


		$this->assertEqual($w1,"WHERE column_name = 12","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($w2,"WHERE ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($w3,"WHERE ( column_name1 = 12 OR column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($w4,"WHERE ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($w5,"WHERE ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		

	}

	function testHavingBlock() {

		db('hosting_dreamhost_tests');

		$h1 = new LMysqlHavingBlock(LMysqlCondition::equal('column_name',12));
		$h2 = new LMysqlHavingBlock(new LMysqlAndBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$h3 = new LMysqlHavingBlock(new LMysqlOrBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$h4 = new LMysqlHavingBlock([LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')]);
		$h5 = new LMysqlHavingBlock(new LMysqlElementList(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));


		$this->assertEqual($h1,"HAVING column_name = 12","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($h2,"HAVING ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($h3,"HAVING ( column_name1 = 12 OR column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($h4,"HAVING ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($h5,"HAVING ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		

	}

	function testOnBlock() {

		db('hosting_dreamhost_tests');

		$o1 = new LMysqlOnBlock(LMysqlCondition::equal('column_name',12));
		$o2 = new LMysqlOnBlock(new LMysqlAndBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$o3 = new LMysqlOnBlock(new LMysqlOrBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$o4 = new LMysqlOnBlock([LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')]);
		$o5 = new LMysqlOnBlock(new LMysqlElementList(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));


		$this->assertEqual($o1," ON column_name = 12","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($o2," ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($o3," ON ( column_name1 = 12 OR column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($o4," ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		$this->assertEqual($o5," ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore addeso dal blocco della where non corrisponde a quello atteso!");
		

	}
}