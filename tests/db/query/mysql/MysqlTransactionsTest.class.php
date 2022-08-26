<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class MysqlTransactionsTest extends LTestCase {
	

	function testCommit() {
		
		$db = db('framework_unit_tests');

		$db->beginTransaction();

		delete('targhetta_albero')->go($db);

		$result = select('count(*) AS C','targhetta_albero')->go($db);

		$this->assertEqual($result[0]['C'],0,"Il numero di risultati trovati non corrisponde!");

		insert('targhetta_albero',['codice_targhetta'],['codice123'])->go($db);

		$db->commit();

		$result = select('count(*) AS C','targhetta_albero')->go($db);

		$this->assertEqual($result[0]['C'],1,"Il numero di risultati trovati non corrisponde!");



	}


	function testRollback() {
		$db = db('framework_unit_tests');

		delete('targhetta_albero')->go($db);

		$result = select('count(*) AS C','targhetta_albero')->go($db);

		$this->assertEqual($result[0]['C'],0,"Il numero di risultati trovati non corrisponde!");

		insert('targhetta_albero',['codice_targhetta'],['codice123'])->go($db);

		$result = select('count(*) AS C','targhetta_albero')->go($db);

		$this->assertEqual($result[0]['C'],1,"Il numero di risultati trovati non corrisponde!");

		$db->beginTransaction();

		delete('targhetta_albero')->go($db);		

		$db->rollback();

		$result = select('count(*) AS C','targhetta_albero')->go($db);

		$this->assertEqual($result[0]['C'],1,"Il numero di risultati trovati non corrisponde!");
	}

	function testSetCharset() {

		$db = db('framework_unit_tests');

		$db->setCharset('utf8');
	}


}