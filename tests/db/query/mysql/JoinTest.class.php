<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class JoinTest extends LTestCase {
	

	function testJoin() {

		$ij1 = LMysqlGenericJoin::inner_join('table_123',LMysqlCondition::equal('field_name',12));
		$ij2 = LMysqlGenericJoin::inner_join('table_123',new LMysqlAndBlock(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));
		$ij3 = LMysqlGenericJoin::inner_join('table_123',new LMysqlOrBlock(LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')));
		$ij4 = LMysqlGenericJoin::inner_join('table_123',[LMysqlCondition::equal('field_name1',12),LMysqlCondition::not_equal('field_name2','ab')]);
		$ij5 = LMysqlGenericJoin::inner_join('table_123',new LMysqlOnBlock(LMysqlCondition::equal('field_name',12)));


		$this->assertEqual($ij1,"INNER JOIN table_123 ON field_name = 12 ","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($ij2,"INNER JOIN table_123 ON ( field_name1 = 12 AND field_name2 != 'ab' ) ","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($ij3,"INNER JOIN table_123 ON ( field_name1 = 12 OR field_name2 != 'ab' ) ","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($ij4,"INNER JOIN table_123 ON ( field_name1 = 12 AND field_name2 != 'ab' ) ","Il valore dell'SQL della join non corrisponde al valore atteso!");
		$this->assertEqual($ij5,"INNER JOIN table_123 ON field_name = 12 ","Il valore dell'SQL della join non corrisponde al valore atteso!");

	}

}