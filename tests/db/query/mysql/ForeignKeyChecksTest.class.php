<?php


class ForeignKeyChecksTest extends LTestCase {
	

	function testForeignKeyChecks() {

		$db = db('framework_unit_tests');

		foreign_key_checks(false)->go($db);

		foreign_key_checks(true)->go($db);


	}


}