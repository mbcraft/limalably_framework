<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class MysqlForeignKeyChecksTest extends LTestCase {
	

	function testForeignKeyChecks() {

		$db = db('hosting_dreamhost_tests');

		foreign_key_checks(false)->go($db);

		foreign_key_checks(true)->go($db);


	}


}