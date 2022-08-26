<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class MysqlForeignKeyChecksTest extends LTestCase {
	

	function testForeignKeyChecks() {

		$db = db('framework_unit_tests');

		foreign_key_checks(false)->go($db);

		foreign_key_checks(true)->go($db);


	}


}