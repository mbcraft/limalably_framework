<?php


class TableListTest extends LTestCase {
	

	function testTableList() {
		$db = db('framework_unit_tests');

		$result = table_list()->go($db);

		$this->assertTrue(count($result)>10,"Il numero di tabelle restituite non corrisponde!");

		$this->assertTrue(array_value_exists($result,'albero'),"La tabella cercata non esiste!");
		$this->assertTrue(array_value_exists($result,'specie_albero'),"La tabella cercata non esiste!");
		$this->assertTrue(array_value_exists($result,'regione'),"La tabella cercata non esiste!");
		$this->assertTrue(array_value_exists($result,'provincia'),"La tabella cercata non esiste!");
		$this->assertTrue(array_value_exists($result,'comune'),"La tabella cercata non esiste!");

	}

}