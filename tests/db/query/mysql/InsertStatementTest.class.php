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



}