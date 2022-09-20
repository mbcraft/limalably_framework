<?php

class TarghettaAlberoAutoDO extends LAbstractDataObject {
	

	const TABLE = "targhetta_albero";

}

class RegioneAutoDO extends LAbstractDataObject {
	const TABLE = "regione";
}

class ProvinciaAutoDO extends LAbstractDataObject {
	const TABLE = "provincia";
}

class ComuneAutoDO extends LAbstractDataObject {
	const TABLE = "comune";
}

class AutoDataObjectTest extends LTestCase {
	

	function testBasicInsertSaveUpdateDelete() {
		
		$db = db('framework_unit_tests');

		truncate('targhetta_albero')->go($db);

		$t1 = new TarghettaAlberoAutoDO();

		$t1->codice_targhetta = "abc1";

		$t1->saveOrUpdate($db);

		$this->assertEqual($t1->id,1,"L'id presente nel data object non corrisponde!");

		$t2 = new TarghettaAlberoAutoDO();

		$t2->codice_targhetta = "abc2";

		$t2->saveOrUpdate($db);

		$this->assertEqual($t2->id,2,"L'id presente nel data object non corrisponde!");

		$result = select('count(*) as C','targhetta_albero')->go($db);

		$this->assertEqual($result[0]['C'],2,"Il numero di righe ritornate non corrisponde!");

		// caricamenti

		$t1_load_2 = new TarghettaAlberoAutoDO(1,$db);

		$this->assertEqual($t1_load_2->codice_targhetta,'abc1',"Il codice della targhetta letto non corrisponde!!");

		$result = TarghettaAlberoAutoDO::findAll()::go();

		//echo $result;

		$this->assertEqual(count($result),2,"Il numero di elementi della classe non corrisponde!");

		$first = TarghettaAlberoAutoDO::findFirst()::go();

		$this->assertTrue($first instanceof TarghettaAlberoAutoDO,"L'oggetto non è della classe attesa!");

		$one = TarghettaAlberoAutoDO::findOne(_eq('id',1))::go();

		$this->assertTrue($first instanceof TarghettaAlberoAutoDO,"L'oggetto non è della classe attesa!");

		$count = TarghettaAlberoAutoDO::count()::go();

		$this->assertEqual($count,2,"Il numero di righe trovate nella tabella non corrisponde a quelle attese!");

	}

	function testNavigation() {

		$db = db('framework_unit_tests');

		foreign_key_checks(false)->go($db);

		truncate('regione')->go($db);

		truncate('provincia')->go($db);

		truncate('comune')->go($db);

		foreign_key_checks(false)->go($db);

		$r1 = new RegioneAutoDO();
		$r1->nome = "RegioneA";
		$r1->codice = "R-A1";
		$r1->saveOrUpdate();

		$p1 = new ProvinciaAutoDO();
		$p1->nome = "ProvinciaA";
		$p1->codice = "P-A1";
		$p1->regione_id = $r1->id;
		$p1->saveOrUpdate();

		$p2 = new ProvinciaAutoDO();
		$p2->nome = "ProvinciaB";
		$p2->codice = "P-B1";
		$p2->regione_id = $r1->id;
		$p2->saveOrUpdate();

		$p_list = $r1->navigateFromOtherTableColumn('regione_id',ProvinciaAutoDO::class);

		$this->assertEqual(count($p_list),2,"Il numero di province ritornate non corrisponde!!");

		$r1_find = $p2->navigateFromColumn('regione_id',RegioneAutoDO::class);

		$this->assertEqual($r1_find->nome,$r1->nome,"Il nome della regione trovata non corrisponde!");
		$this->assertEqual($r1_find->codice,$r1->codice,"Il codice della regione trovata non corrisponde!");


	}
}