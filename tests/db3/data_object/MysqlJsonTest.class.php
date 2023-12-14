<?php


class MyJsonDO extends LAbstractDataObject {

	const MY_TABLE = "my_json_table";

	const MY_JSON_COLUMNS = ['dati'];

	public $id;
	public $cliente_id;
	public $dati;
	public $conteggio_ore;

}

class MysqlJsonTest extends LTestCase {
	

	function testJson() {

		$db = db('hosting_dreamhost_tests');

		drop_table('my_json_table')->if_exists()->go($db);

		create_table('my_json_table')->if_not_exists()
			->column(col_def('id')->t_id())
			->column(col_def('cliente_id')->t_external_id())
			->column(col_def('dati')->t_json())
			->column(col_def('conteggio_ore')->t_u_int())
			->go($db);

		$prova = array("chiave" => "valore","hello" => "world","int" => 3);

		insert('my_json_table',['dati'],[json_encode($prova,true)])->go($db);

		$current_data = select('*','my_json_table')->go($db);

		$do = new MyJsonDO(1);

		$this->assertTrue(is_array($do->dati),"Il campo dati non risulta essere un array! : ".var_export($do->dati,true));

		$do->dati['hello'] = "planet";

		$do->saveOrUpdate();

		$do = new MyJsonDO(1);

		$this->assertEqual($do->dati['hello'],"planet","Il dato è stato salvato misteriosamente!");

		drop_table('my_json_table')->if_exists($db);
	}

}