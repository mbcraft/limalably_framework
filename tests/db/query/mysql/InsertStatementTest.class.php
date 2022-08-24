<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class InsertStatementTest extends LTestCase {
	

	function testInsertStatement() {


		db('framework_unit_tests');

		$i1 = insert('table_name123',['c1','c2','c3'],['a','b','c']);
		$i2 = insert('table_abc',['c1','c2','c3'],[['a','b','c'],['d','e','f']]);

		$this->assertEqual($i1,"INSERT INTO table_name123 (c1,c2,c3) VALUES ('a','b','c')","Lo statement SQL di insert non corrisponde al valore atteso!");
		$this->assertEqual($i2,"INSERT INTO table_abc (c1,c2,c3) VALUES ('a','b','c'),('d','e','f')","Lo statement SQL di insert non corrisponde al valore atteso!");

	}

	function testInsertWithDifferentModes() {

		$db = db('framework_unit_tests');

		drop_table('my_test')->if_exists()->go($db);

		create_table('my_test')->column(col_def('id')->t_id())->column(col_def('testo')->t_text32())->column(col_def('valore_int')->t_u_int())->go($db);

		insert('my_test',['testo','valore_int'],['abcd1',12])->go($db);

		insert('my_test')->column_list('testo','valore_int')->data(['abcd2',34])->go($db);

		insert('my_test')->column_list(['testo','valore_int'])->data(['abcd2',12])->go($db);

		insert('my_test')->column_list(['testo','valore_int'])->data([['abcd2',12],['abcd3',34]])->go($db);

	}

	function testInsertWithStrangeCharacters() {
		$db = db('framework_unit_tests');

		drop_table('my_test')->if_exists()->go($db);

		create_table('my_test')->column(col_def('id')->t_id())->column(col_def('testo')->t_text32())->column(col_def('valore_int')->t_u_int())->go($db);

		insert('my_test')->column_list('testo','valore_int')->data(["A'B\"",12])->go($db);

		$result = select('*','my_test')->go($db);

		$this->assertEqual($result[0]['testo'],"A'B\"","Il valore ritornato dalla select non corrisponde a quello inserito in tabella!");
	}



}