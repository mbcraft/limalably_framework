<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MysqlDeleteStatementTest extends LTestCase {
	

	function testDeleteStatement() {

		db('hosting_dreamhost_tests');

		$d1 = delete('table_123')->where(_eq('column_name',12));
		$d2 = delete('table_123')->where(_eq('column_name',12))->inner_join(true,'table_for_join');
		$d3 = delete('table_123')->where(_eq('column_name',12))->inner_join(false,'table_for_join',_eq('field_j1',3));
		$d4 = delete('table_123')->where(_eq('column_name',12))->order_by('a','b')->limit(5);
		$d5 = delete('table_123')->where(_eq('column_name',12))->order_by('a','b');
		$d6 = delete('table_123')->where(_eq('column_name',12))->limit(5);
		
		$this->assertEqual($d1,"DELETE FROM table_123 WHERE column_name = 12","Il valore dell'SQL della query di delete non corrisponde al valore atteso!");
		$this->assertEqual($d2,"DELETE table_123,table_for_join FROM table_123 INNER JOIN table_for_join WHERE column_name = 12","Il valore dell'SQL della query di delete non corrisponde al valore atteso!");
		$this->assertEqual($d3,"DELETE table_123 FROM table_123 INNER JOIN table_for_join ON field_j1 = 3 WHERE column_name = 12","Il valore dell'SQL della query di delete non corrisponde al valore atteso!");
		$this->assertEqual($d4,"DELETE FROM table_123 WHERE column_name = 12 ORDER BY a,b LIMIT 5","Il valore dell'SQL della query di delete non corrisponde al valore atteso!");
		
		try {
			echo $d5;
			$this->fail("La query SQL configurata erroneamente è una stringa valida!");
		} catch (\Exception $ex)
		{		
			//ok
		}

		try {
			echo $d6;
			$this->fail("La query SQL configurata erroneamente è una stringa valida!");
		} catch (\Exception $ex)
		{		
			//ok
		}
	}

}