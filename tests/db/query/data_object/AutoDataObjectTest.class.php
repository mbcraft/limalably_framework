<?php

class TarghettaAlberoAutoDO extends LAbstractDataObject {
	

	const TABLE = "targhetta_albero";

}

class AutoDataObjectTest extends LTestCase {
	

	function testBasicInsertSaveUpdateDelete() {
		
		$db = db('framework_unit_tests');

		truncate('targhetta_albero')->go($db);

		$t1 = new TarghettaAlberoAutoDO();

		$t1->codice_targhetta = "abc123";

		$t1->saveOrUpdate($db);

		$this->assertEqual($t1->id,1,"L'id presente nel data object non corrisponde!");

		$t2 = new TarghettaAlberoAutoDO();

		$t2->codice_targhetta = "abc123";

		$t2->saveOrUpdate($db);

		$this->assertEqual($t2->id,2,"L'id presente nel data object non corrisponde!");

		$result = select('count(*) as C','targhetta_albero')->go($db);

		$this->assertEqual($result[0]['C'],2,"Il numero di righe ritornate non corrisponde!");
	}
}