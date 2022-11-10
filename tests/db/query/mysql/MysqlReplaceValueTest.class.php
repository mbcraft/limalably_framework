<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MysqlReplaceValueTest extends LTestCase {
	

	function testReplaceValue() {

		db('hosting_dreamhost_tests');

		$r1 = _repl('column_name','@search_value','@replace_value');
		$r2 = _replace_value('column_name','@search_value','@replace_value');

		$this->assertEqual($r1,"REPLACE(column_name,'@search_value','@replace_value')","Il valore atteso dalla _repl non corrisponde a quello atteso!");
		$this->assertEqual($r2,"REPLACE(column_name,'@search_value','@replace_value')","Il valore atteso dalla _replace_value non corrisponde a quello atteso!");
		
	}

}