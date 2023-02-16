<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MysqlExpressionTest extends LTestCase {
	
	function testBasicExpressionUsage() {

		$db = db('hosting_dreamhost_tests');

		drop_table('my_expr_test')->if_exists()->go($db);

		create_table('my_expr_test')->column(col_def('id')->t_id())->column(col_def('my_time')->t_timestamp())->go($db);

		insert('my_expr_test',['my_time'],[_expr('NOW()')])->go($db);

		$result = select('my_time','my_expr_test')->go($db);

		

		$this->assertTrue(strpos($result[0]['my_time'],'2')!==false,"Il tempo non è stato scritto correttamente sul database!");

	}


}