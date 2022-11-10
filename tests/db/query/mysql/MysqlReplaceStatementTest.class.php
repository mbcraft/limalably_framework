<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class MysqlReplaceStatementTest extends LTestCase {
	
	function testReplaceStatement() {
		$db = db('hosting_dreamhost_tests');

		drop_table('my_test')->if_exists()->go($db);

		create_table('my_test')->column(col_def('id')->t_id())->column(col_def('testo')->t_text32())->column(col_def('valore_int')->t_u_int())->go($db);

		insert('my_test',['testo','valore_int'],['abcd1',12])->go($db);

		insert('my_test')->column_list('testo','valore_int')->data(['abcd2',34])->go($db);

		insert('my_test')->column_list(['testo','valore_int'])->data(['abcd2',12])->go($db);

		insert('my_test')->column_list(['testo','valore_int'])->data([['abcd2',12],['abcd3',34]])->go($db);

		replace('my_test')->column_list(['id','testo','valore_int'])->data([[1,'abcd2z',123],[2,'abcd3z',345]])->go($db);

		$col_num = last_affected_rows()->go($db);

		$this->assertEqual($col_num,4,"Il numero delle righe modificate non corrisponde!!");
	
		replace('my_test')->data(['id' => 1,'testo' => 'abcd2z','valore_int' => 123])->go($db);

		$col_num = last_affected_rows()->go($db);

		$this->assertEqual($col_num,2,"Il numero delle righe modificate non corrisponde!!");

		replace('my_test',['id','testo','valore_int'],[[1,'abcd2z',123],[2,'abcd3z',345]])->go($db);
	}

}