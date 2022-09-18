<?php

class TarghettaAlberoManualDO extends LAbstractDataObject {
	

	const TABLE = "targhetta_albero";

	public $id;
	public $codice_targhetta;

}

class ManualDataObjectTest extends LTestCase {
	

	function testBasicInsertSaveUpdateDelete() {

		$db = db('framework_unit_tests');

		truncate('targhetta_albero')->go($db);

		$t1 = new TarghettaAlberoManualDO();

		$t1->codice_targhetta = "abc123";

		$t1->saveOrUpdate($db);

		$this->assertEqual($t1->id,1,"L'id presente nel data object non corrisponde!");

		$t2 = new TarghettaAlberoManualDO();

		$t2->codice_targhetta = "abc123";

		$t2->saveOrUpdate($db);

		$this->assertEqual($t2->id,2,"L'id presente nel data object non corrisponde!");

		$result = select('count(*) as C','targhetta_albero')->go($db);

		$this->assertEqual($result[0]['C'],2,"Il numero di righe ritornate non corrisponde!");

		$result = TarghettaAlberoManualDO::findAll()::go();

		$this->assertEqual(count($result),2,"Il numero di elementi della classe non corrisponde!");

		$first = TarghettaAlberoManualDO::findFirst()::go();

		$this->assertTrue($first instanceof TarghettaAlberoManualDO,"L'oggetto non è della classe attesa!");

	}

}