<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MysqlUpdateStatementTest extends LTestCase {
	

	function testUpdateStatement() {

		db("hosting_dreamhost_tests");

		$u1 = update('table_name123',['a' => 1,'b' => 2]);
		$u2 = update('table_name123',['a' => 1,'b' => 2])->where(_eq('c',3));
		$u3 = update('table_name123',['a' => 1,'b' => 2])->where([_eq('c',3),_n_eq('d',4)]);
		$u4 = update('table_name123',['a' => 1,'b' => 2])->where(_eq('c',3),_n_eq('d',4));
		$u5 = update('table_name123',['a' => 1,'b' => 2],_eq('c',3));
		$u6 = update('table_name123',['a' => 1,'b' => 2],[_eq('c',3),_n_eq('d',4)]);
		$u7 = update('table_name123',['a' => 1,'b' => 2],_or(_eq('c',3),_n_eq('d',4)));


		$this->assertEqual($u1,"UPDATE table_name123 SET a = 1,b = 2","Lo statement SQL di update non corrisponde al valore atteso!");
		$this->assertEqual($u2,"UPDATE table_name123 SET a = 1,b = 2 WHERE c = 3","Lo statement SQL di update non corrisponde al valore atteso!");
		$this->assertEqual($u3,"UPDATE table_name123 SET a = 1,b = 2 WHERE ( c = 3 AND d != 4 )","Lo statement SQL di update non corrisponde al valore atteso!");
		$this->assertEqual($u4,"UPDATE table_name123 SET a = 1,b = 2 WHERE ( c = 3 AND d != 4 )","Lo statement SQL di update non corrisponde al valore atteso!");
		$this->assertEqual($u5,"UPDATE table_name123 SET a = 1,b = 2 WHERE c = 3","Lo statement SQL di update non corrisponde al valore atteso!");
		$this->assertEqual($u6,"UPDATE table_name123 SET a = 1,b = 2 WHERE ( c = 3 AND d != 4 )","Lo statement SQL di update non corrisponde al valore atteso!");
		$this->assertEqual($u7,"UPDATE table_name123 SET a = 1,b = 2 WHERE ( c = 3 OR d != 4 )","Lo statement SQL di update non corrisponde al valore atteso!");
			
	}

}