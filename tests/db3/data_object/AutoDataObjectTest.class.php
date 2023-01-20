<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class TarghettaAlberoAutoDO extends LAbstractDataObject {
	
	const MY_TABLE = "targhetta_albero";
}

class RegioneAutoDO extends LAbstractDataObject {
	const MY_TABLE = "regione";
}

class ProvinciaAutoDO extends LAbstractDataObject {
	const MY_TABLE = "provincia";
}

class ComuneAutoDO extends LAbstractDataObject {
	const MY_TABLE = "comune";
}

class ProvaDO extends LAbstractDataObject {
	const MY_TABLE = "my_soft_prova";

	const HAS_STANDARD_OPERATIONS_COLUMNS = true;
}

class ProvaOrderingDO extends LAbstractDataObject {
	const MY_TABLE = "my_prova_ordering";

	const MY_ORDER_COLUMN = "order_val";

	const MY_ORDER_GROUP_COLUMNS = ["categoria_id"];
}

class ProvaOrderingStdColsDO extends LAbstractDataObject {
	const MY_TABLE = "my_prova_ordering_std_cols";

	const MY_ORDER_COLUMN = "order_val";

	const MY_ORDER_GROUP_COLUMNS = ["categoria_id"];

	const HAS_STANDARD_OPERATIONS_COLUMNS = true;

}

class AutoDataObjectTest extends LTestCase {
	

	function testBasicInsertSaveUpdateDelete() {
		
		$db = db('hosting_dreamhost_tests');

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

		$do = new TarghettaAlberoAutoDO();

		$result = $do->findAll()->go();

		//echo $result;

		$this->assertEqual(count($result),2,"Il numero di elementi della classe non corrisponde!");

		$first = $do->findFirst()->go();

		$this->assertTrue($first instanceof TarghettaAlberoAutoDO,"L'oggetto non è della classe attesa!");

		$one = $do->findOne(_eq('id',1))->go();

		$this->assertTrue($first instanceof TarghettaAlberoAutoDO,"L'oggetto non è della classe attesa!");

		$count = $do->count()->go();

		$this->assertEqual($count,2,"Il numero di righe trovate nella tabella non corrisponde a quelle attese!");

	}

	function testNavigation() {

		$db = db('hosting_dreamhost_tests');

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


	public function testProvaSoftColumns() {


		$db = db('hosting_dreamhost_tests');

		drop_table('my_soft_prova')->if_exists()->go($db);

		create_table('my_soft_prova')->column(col_def('id')->t_id())
		->column(col_def('text_value')->t_text32())
		->column(col_def('int_value')->t_u_int())
		->standard_operations_columns()->go($db);

		$a1 = new ProvaDO();
		$a1->text_value = "qualcosa";
		$a1->int_value = 12;

		$a1->created_by(3);
		$a1->saveOrUpdate();

		$a1_again = new ProvaDO(1);
		$a1_again->last_updated_by(17);
		$a1_again->saveOrUpdate();

		$a1_again->soft_delete(7);


		$this->assertEqual($a1_again->created_by,3,"L'id del created by non corrisponde!");
		$this->assertEqual($a1_again->last_updated_by,17,"L'id del created by non corrisponde!");
		$this->assertEqual($a1_again->deleted_by,7,"L'id del created by non corrisponde!");
		$this->assertNotNull($a1_again->deleted_at,"La data del delete non è nulla!");

		$a1_again->soft_undelete();

		$this->assertNull($a1_again->deleted_at,"L'id del soft delete non è nullo dopo l'undelete!");
		$this->assertNull($a1_again->deleted_by,"La data del soft delete non è nulla dopo l'undelete!");

		$a2 = new ProvaDO();
		$a2->text_value = "qualcosa";
		$a2->int_value = 15;

		$a2->created_by();
		$a2->saveOrUpdate();

		$a2->soft_delete();

		$do = new ProvaDO();

		$all_results = $do->findAll()->go();

		$this->assertTrue(count($all_results)==1,"Il numero dei risultati non corrisponde!");

		$all_results = $do->findAll()->with_soft_deleted()->go();

		$this->assertTrue(count($all_results)==2,"Il numero dei risultati non corrisponde!");

		$all_results = $do->findAll()->only_soft_deleted()->go();

		$this->assertTrue(count($all_results)==1,"Il numero dei risultati non corrisponde!");

		//adding a condition to test also the other part

		$all_results = $do->findAll(_eq('text_value','qualcosa'))->go();

		$this->assertTrue(count($all_results)==1,"Il numero dei risultati non corrisponde!");

		$all_results = $do->findAll(_eq('text_value','qualcosa'))->with_soft_deleted()->go();

		$this->assertTrue(count($all_results)==2,"Il numero dei risultati non corrisponde!");

		$all_results = $do->findAll(_eq('text_value','qualcosa'))->only_soft_deleted()->go();

		$this->assertTrue(count($all_results)==1,"Il numero dei risultati non corrisponde!");

		drop_table('my_soft_prova')->if_exists()->go($db);


	}

	public function testProvaOrdering() {
		$db = db('hosting_dreamhost_tests');

		drop_table('my_prova_ordering')->if_exists()->go($db);

		create_table('my_prova_ordering')->column(col_def('id')->t_id())
		->column(col_def('text_value')->t_text32())
		->column(col_def('int_value')->t_u_int())
		->column(col_def('categoria_id')->t_u_int())
		->column(col_def('order_val')->t_u_int())->go($db);

		$p1 = new ProvaOrderingDO();
		$p1->int_value = 1;
		$p1->categoria_id = 3;
		$p1->saveOrUpdate();

		$p2 = new ProvaOrderingDO();
		$p2->int_value = 2;
		$p2->categoria_id = 3;
		$p2->saveOrUpdate();

		$p3 = new ProvaOrderingDO();
		$p3->int_value = 3;
		$p3->categoria_id = 3;
		$p3->saveOrUpdate();

		$p4 = new ProvaOrderingDO();
		$p4->int_value = 4;
		$p4->categoria_id = 1;
		$p4->saveOrUpdate();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_last();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p1->move_to_first();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_next();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p1->move_to_previous();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_previous();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p3->move_to_next();
		$p2->reload();
		$p1->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_first();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p3->move_to_last();
		$p2->reload();
		$p1->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p4->delete();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		
		$p1->delete();
		$p2->reload();
		$p3->reload();

		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,2,"Il valore dell'ordine non corrisponde!");

		$p2->delete();
		$p3->reload();

		$this->assertEqual($p3->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p3->delete();

	}

	public function testProvaOrderingStandardCols() {
		$db = db('hosting_dreamhost_tests');

		drop_table('my_prova_ordering_std_cols')->if_exists()->go($db);

		create_table('my_prova_ordering_std_cols')->column(col_def('id')->t_id())
		->column(col_def('text_value')->t_text32())
		->column(col_def('int_value')->t_u_int())
		->column(col_def('categoria_id')->t_u_int())
		->column(col_def('order_val')->t_u_int())
		->standard_operations_columns()
		->go($db);

		$p1 = new ProvaOrderingStdColsDO();
		$p1->int_value = 1;
		$p1->categoria_id = 3;
		$p1->saveOrUpdate();

		$p2 = new ProvaOrderingStdColsDO();
		$p2->int_value = 2;
		$p2->categoria_id = 3;
		$p2->saveOrUpdate();

		$p3 = new ProvaOrderingStdColsDO();
		$p3->int_value = 3;
		$p3->categoria_id = 3;
		$p3->saveOrUpdate();

		$p4 = new ProvaOrderingStdColsDO();
		$p4->int_value = 4;
		$p4->categoria_id = 1;
		$p4->saveOrUpdate();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_last();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p1->move_to_first();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_next();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p1->move_to_previous();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_previous();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p3->move_to_next();
		$p2->reload();
		$p1->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_first();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p3->move_to_last();
		$p2->reload();
		$p1->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p4->delete();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		
		$p1->delete();
		$p2->reload();
		$p3->reload();

		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,2,"Il valore dell'ordine non corrisponde!");

		$p2->delete();
		$p3->reload();

		$this->assertEqual($p3->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p3->delete();
		
			
	}

	public function testProvaOrderingWithNullCategory() {
		$db = db('hosting_dreamhost_tests');

		drop_table('my_prova_ordering')->if_exists()->go($db);

		create_table('my_prova_ordering')->column(col_def('id')->t_id())
		->column(col_def('text_value')->t_text32())
		->column(col_def('int_value')->t_u_int())
		->column(col_def('categoria_id')->t_u_int())
		->column(col_def('order_val')->t_u_int())->go($db);

		$p1 = new ProvaOrderingDO();
		$p1->int_value = 1;
		$p1->categoria_id = null;
		$p1->saveOrUpdate();

		$p2 = new ProvaOrderingDO();
		$p2->int_value = 2;
		$p2->categoria_id = null;
		$p2->saveOrUpdate();

		$p3 = new ProvaOrderingDO();
		$p3->int_value = 3;
		$p3->categoria_id = null;
		$p3->saveOrUpdate();

		$p4 = new ProvaOrderingDO();
		$p4->int_value = 4;
		$p4->categoria_id = 1;
		$p4->saveOrUpdate();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_last();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p1->move_to_first();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_next();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p1->move_to_previous();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_previous();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p3->move_to_next();
		$p2->reload();
		$p1->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_first();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p3->move_to_last();
		$p2->reload();
		$p1->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p4->delete();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		
		$p1->delete();
		$p2->reload();
		$p3->reload();

		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,2,"Il valore dell'ordine non corrisponde!");

		$p2->delete();
		$p3->reload();

		$this->assertEqual($p3->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p3->delete();

	}

	public function testProvaOrderingStandardColsWithNullCategory() {
		$db = db('hosting_dreamhost_tests');

		drop_table('my_prova_ordering_std_cols')->if_exists()->go($db);

		create_table('my_prova_ordering_std_cols')->column(col_def('id')->t_id())
		->column(col_def('text_value')->t_text32())
		->column(col_def('int_value')->t_u_int())
		->column(col_def('categoria_id')->t_u_int())
		->column(col_def('order_val')->t_u_int())
		->standard_operations_columns()
		->go($db);

		$p1 = new ProvaOrderingStdColsDO();
		$p1->int_value = 1;
		$p1->categoria_id = null;
		$p1->saveOrUpdate();

		$p2 = new ProvaOrderingStdColsDO();
		$p2->int_value = 2;
		$p2->categoria_id = null;
		$p2->saveOrUpdate();

		$p3 = new ProvaOrderingStdColsDO();
		$p3->int_value = 3;
		$p3->categoria_id = null;
		$p3->saveOrUpdate();

		$p4 = new ProvaOrderingStdColsDO();
		$p4->int_value = 4;
		$p4->categoria_id = 1;
		$p4->saveOrUpdate();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_last();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p1->move_to_first();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_next();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p1->move_to_previous();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_previous();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p3->move_to_next();
		$p2->reload();
		$p1->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p1->move_to_first();
		$p2->reload();
		$p3->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p3->move_to_last();
		$p2->reload();
		$p1->reload();
		$p4->reload();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p4->order_val,1,"Il valore dell'ordine non corrisponde!");

		$p4->delete();

		$this->assertEqual($p1->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p2->order_val,2,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,3,"Il valore dell'ordine non corrisponde!");
		
		$p1->delete();
		$p2->reload();
		$p3->reload();

		$this->assertEqual($p2->order_val,1,"Il valore dell'ordine non corrisponde!");
		$this->assertEqual($p3->order_val,2,"Il valore dell'ordine non corrisponde!");

		$p2->delete();
		$p3->reload();

		$this->assertEqual($p3->order_val,1,"Il valore dell'ordine non corrisponde!");
		
		$p3->delete();
		
			
	}
}