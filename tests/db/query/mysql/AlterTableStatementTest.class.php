<?php


class AlterTableStatementTest extends LTestCase {
	

	function testBasicColumnsOperations() {

		$db = db('framework_unit_tests');

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

		$l = table_list()->go($db);

		$this->assertTrue(array_value_exists('big_table',$l),"La tabella non è stata creata!");

		alter_table('big_table')->drop_column('cliente_id')->drop_column('descrizione')->go($db);

		$td = table_description('big_table')->go($db);

		$this->assertTrue(array_key_exists('id',$td),"La colonna id nella tabella big_table non esiste!");
		$this->assertTrue(array_key_exists('data_inizio',$td),"La colonna id nella tabella big_table non esiste!");
		$this->assertFalse(array_key_exists('cliente_id',$td),"La colonna id nella tabella big_table non esiste!");
		$this->assertFalse(array_key_exists('descrizione',$td),"La colonna id nella tabella big_table non esiste!");

		alter_table('big_table')->add_column(col_def('prova')->t_text()->after('conteggio_ore'))->go($db);

		$td = table_description('big_table')->go($db);
		
		$this->assertTrue(array_key_exists('prova',$td),"La colonna 'prova' nella tabella big_table non esiste!");
		
		alter_table('big_table')->change_column('prova',col_def('prova_2')->t_u_bigint()->not_null()->after('conteggio_ore'))->go($db);

		$td = table_description('big_table')->go($db);
		
		$this->assertFalse(array_key_exists('prova',$td),"La colonna 'prova' nella tabella big_table non esiste!");
		$this->assertTrue(array_key_exists('prova_2',$td),"La colonna 'prova_2' nella tabella big_table non esiste!");
			
		alter_table('big_table')->modify_column(col_def('prova_2')->t_text())->go($db);

		$this->assertTrue(array_key_exists('prova_2',$td),"La colonna 'prova_2' nella tabella big_table non esiste!");
		
	}

	function testForeignKeysOperations() {
		$db = db('framework_unit_tests');

		drop_table('cliente_test')->if_exists()->go($db);

		drop_table('fattura_test')->if_exists()->go($db);

		create_table('cliente_test')->column(col_def('id')->t_id())->column(col_def('nome')->t_text32())->go($db);

		create_table('fattura_test')->column(col_def('id')->t_id())->column(col_def('importo')->t_float())
			->column(col_def('cliente_id')->t_external_id())->foreign_key(fk_def('fk_test_cliente_id')->ref_columns('cliente_id')->ref_table('cliente_test','id')->on_delete_cascade()->on_update_restrict())->go($db);


		drop_table('cliente_test')->if_exists()->go($db);

		drop_table('fattura_test')->if_exists()->go($db);

		create_table('cliente_test')->column(col_def('id')->t_id())->column(col_def('nome')->t_text32())->go($db);

		create_table('fattura_test')->column(col_def('id')->t_id())->column(col_def('importo')->t_float())
			->column(col_def('cliente_id')->t_external_id())->go($db);

		alter_table('fattura_test')->add_foreign_key(fk_def('fk_test_cliente_id')->ref_columns('cliente_id')->ref_table('cliente_test','id')->on_delete_cascade()->on_update_restrict())->go($db);

		drop_table('cliente_test')->if_exists()->go($db);

		drop_table('fattura_test')->if_exists()->go($db);
	}


}