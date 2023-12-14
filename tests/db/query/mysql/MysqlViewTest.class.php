<?php


class MysqlViewTest extends LTestCase {
	

	function testCreateDropView() {


		$db = db('hosting_dreamhost_tests');

		drop_table('big_table')->if_exists()->go($db);

		$l = table_list()->go($db);

		$this->assertFalse(array_value_exists('big_table',$l),"La tabella esiste prima di essere creata!");

		create_table('big_table')
			->column(col_def('id')->t_id())
			->column(col_def('data_inizio')->t_date()->not_null())
			->column(col_def('data_fine')->t_date())
			->column(col_def('cliente_id')->t_external_id()->not_null())
			->column(col_def('descrizione')->t_text())
			->column(col_def('conteggio_ore')->t_u_int()->not_null())
			->go($db);


		$c = create_view("my_big_table")->or_replace()->as(select('*','big_table'))->go($db);

		$vl = view_list()->go($db);

		$this->assertTrue(in_array('my_big_table',$vl),"La vista non è fra i risultati!");

		$d = drop_view("my_big_table")->go($db);



		drop_table('big_table')->go($db);

	}

}