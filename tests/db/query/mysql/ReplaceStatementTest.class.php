<?php


class ReplaceStatementTest extends LTestCase {
	
	function testReplaceStatement() {
		$db = db('framework_unit_tests');

		drop_table('my_test')->if_exists()->go($db);

		create_table('my_test')->column(col_def('id')->t_id())->column(col_def('testo')->t_text32())->column(col_def('valore_int')->t_u_int())->go($db);

		insert('my_test',['testo','valore_int'],['abcd1',12])->go($db);

		insert('my_test')->column_list('testo','valore_int')->data(['abcd2',34])->go($db);

		insert('my_test')->column_list(['testo','valore_int'])->data(['abcd2',12])->go($db);

		insert('my_test')->column_list(['testo','valore_int'])->data([['abcd2',12],['abcd3',34]])->go($db);
	}

}