<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MysqlFunctionsTest extends LTestCase {
	

	function testIsNull() {

		db('hosting_dreamhost_tests');

		$result = _ifnull('my_column_name','ABC');

		$this->assertEqual($result,"IFNULL ( my_column_name , 'ABC' )","Il risultato della funzione mysql non coincide con quello atteso!");
	}

	function testCaseFunction() {
		$db = db('hosting_dreamhost_tests');

		drop_table('my_test_table')->if_exists()->go($db);

		create_table('my_test_table')->column(col_def('id')->t_id())->column(col_def('value_col')->t_u_int())->go($db);

		insert('my_test_table',['value_col'],[[1],[2],[3]])->go($db);

		$q = select(
			_case('CVAL')
			->when(_eq('value_col',1),'A')
			->when(_eq('value_col',2),'B')
			->when(_eq('value_col',3),'C')->default('D'),'my_test_table');

		$result = $q->go($db);

		$this->assertEqual(count($result),3,"Il numero dei risultati ritornati non corrisponde!");

		$this->assertEqual($result[0]['CVAL'],'A',"Il valore della prima riga non corrisponde!");
		$this->assertEqual($result[1]['CVAL'],'B',"Il valore della seconda riga non corrisponde!");
		$this->assertEqual($result[2]['CVAL'],'C',"Il valore della terza riga non corrisponde!");
			
	}

}