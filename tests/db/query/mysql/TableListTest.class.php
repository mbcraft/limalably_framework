<?php


class TableListTest extends LTestCase {
	

	function testTableList() {
		$db = db('framework_unit_tests');

		$result = table_list()->go($db);

		$this->assertEqual(count($result),11,"Il numero di tabelle restituite non corrisponde!");
	}

}