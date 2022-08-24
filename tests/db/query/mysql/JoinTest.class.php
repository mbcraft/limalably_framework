<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class JoinTest extends LTestCase {
	

	function testInnerJoin() {

		$ij1 = LMysqlGenericJoin::inner_join('table_123',LMysqlCondition::equal('column_name',12));
		$ij2 = LMysqlGenericJoin::inner_join('table_123',new LMysqlAndBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$ij3 = LMysqlGenericJoin::inner_join('table_123',new LMysqlOrBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$ij4 = LMysqlGenericJoin::inner_join('table_123',[LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')]);
		$ij5 = LMysqlGenericJoin::inner_join('table_123',new LMysqlOnBlock(LMysqlCondition::equal('column_name',12)));


		$this->assertEqual($ij1,"INNER JOIN table_123 ON column_name = 12","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($ij2,"INNER JOIN table_123 ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($ij3,"INNER JOIN table_123 ON ( column_name1 = 12 OR column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($ij4,"INNER JOIN table_123 ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($ij5,"INNER JOIN table_123 ON column_name = 12","Il valore dell'SQL della join non corrisponde al valore atteso!");

	}

	function testLeftJoin() {

		$lj1 = LMysqlGenericJoin::left_join('table_123',LMysqlCondition::equal('column_name',12));
		$lj2 = LMysqlGenericJoin::left_join('table_123',new LMysqlAndBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$lj3 = LMysqlGenericJoin::left_join('table_123',new LMysqlOrBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$lj4 = LMysqlGenericJoin::left_join('table_123',[LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')]);
		$lj5 = LMysqlGenericJoin::left_join('table_123',new LMysqlOnBlock(LMysqlCondition::equal('column_name',12)));


		$this->assertEqual($lj1,"LEFT JOIN table_123 ON column_name = 12","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($lj2,"LEFT JOIN table_123 ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($lj3,"LEFT JOIN table_123 ON ( column_name1 = 12 OR column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($lj4,"LEFT JOIN table_123 ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($lj5,"LEFT JOIN table_123 ON column_name = 12","Il valore dell'SQL della join non corrisponde al valore atteso!");

	}

	function testRightJoin() {

		$rj1 = LMysqlGenericJoin::right_join('table_123',LMysqlCondition::equal('column_name',12));
		$rj2 = LMysqlGenericJoin::right_join('table_123',new LMysqlAndBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$rj3 = LMysqlGenericJoin::right_join('table_123',new LMysqlOrBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$rj4 = LMysqlGenericJoin::right_join('table_123',[LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')]);
		$rj5 = LMysqlGenericJoin::right_join('table_123',new LMysqlOnBlock(LMysqlCondition::equal('column_name',12)));


		$this->assertEqual($rj1,"RIGHT JOIN table_123 ON column_name = 12","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($rj2,"RIGHT JOIN table_123 ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($rj3,"RIGHT JOIN table_123 ON ( column_name1 = 12 OR column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($rj4,"RIGHT JOIN table_123 ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($rj5,"RIGHT JOIN table_123 ON column_name = 12","Il valore dell'SQL della join non corrisponde al valore atteso!");

	}

	function testCrossJoin() {

		$cj1 = LMysqlGenericJoin::cross_join('table_123',LMysqlCondition::equal('column_name',12));
		$cj2 = LMysqlGenericJoin::cross_join('table_123',new LMysqlAndBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$cj3 = LMysqlGenericJoin::cross_join('table_123',new LMysqlOrBlock(LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')));
		$cj4 = LMysqlGenericJoin::cross_join('table_123',[LMysqlCondition::equal('column_name1',12),LMysqlCondition::not_equal('column_name2','ab')]);
		$cj5 = LMysqlGenericJoin::cross_join('table_123',new LMysqlOnBlock(LMysqlCondition::equal('column_name',12)));


		$this->assertEqual($cj1,"CROSS JOIN table_123 ON column_name = 12","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($cj2,"CROSS JOIN table_123 ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($cj3,"CROSS JOIN table_123 ON ( column_name1 = 12 OR column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($cj4,"CROSS JOIN table_123 ON ( column_name1 = 12 AND column_name2 != 'ab' )","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($cj5,"CROSS JOIN table_123 ON column_name = 12","Il valore dell'SQL della join non corrisponde al valore atteso!");

	}

}