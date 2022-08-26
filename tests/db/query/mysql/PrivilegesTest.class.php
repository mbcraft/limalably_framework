<?php



class PrivilegesTest extends LTestCase {
	


	function testPrivileges() {
		$db = db('framework_unit_tests');

		$result = privileges_list()->go($db);

		$this->assertTrue(count($result)>10,"Non ci sono privilegi a sufficienza per validare la query!");

		foreach ($result as $el) {
			$this->assertTrue($el instanceof LMysqlPrivilegeDescription,"L'oggetto ritornato non è della classe corretta!");
		}
	}


}