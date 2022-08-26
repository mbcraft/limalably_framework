<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class MysqlTableListTest extends LTestCase {
	

	function testTableList() {
		$db = db('framework_unit_tests');

		$result = table_list()->go($db);

		$this->assertTrue(count($result)>10,"Il numero di tabelle restituite non corrisponde!");

		$this->assertTrue(array_value_exists('albero',$result),"La tabella cercata non esiste!");
		$this->assertTrue(array_value_exists('specie_albero',$result),"La tabella cercata non esiste!");
		$this->assertTrue(array_value_exists('regione',$result),"La tabella cercata non esiste!");
		$this->assertTrue(array_value_exists('provincia',$result),"La tabella cercata non esiste!");
		$this->assertTrue(array_value_exists('comune',$result),"La tabella cercata non esiste!");

	}

}