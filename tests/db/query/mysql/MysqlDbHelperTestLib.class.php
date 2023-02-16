<?php


class MysqlDbHelperTestLib {
	

	static function regenerateDb() {


		$db = db('hosting_dreamhost_tests');

		foreign_key_checks(false)->go($db);

		drop_table('targhetta_albero')->if_exists()
		->go($db);

		create_table('targhetta_albero')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('codice_targhetta')->t_text256()->not_null())
		->engine_innodb()
		->go($db);

		drop_table('regione')->if_exists()
		->go($db);

		create_table('regione')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('nome')->t_text32()->not_null())
		->column(col_def('codice')->t_text32()->not_null())
		->go($db);

		drop_table('provincia')->if_exists()
		->go($db);

		create_table('provincia')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('nome')->t_text32()->not_null())
		->column(col_def('codice')->t_text32()->not_null())
		->column(col_def('regione_id')->t_external_id()->not_null())
		->foreign_key(fk_def('fk_regione_id')->ref_table('regione','id')->ref_columns('regione_id'))
		->go($db);

		drop_table('comune')->if_exists()
		->go($db);

		create_table('comune')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('nome')->t_text32()->not_null())
		->column(col_def('codice')->t_text32()->not_null())
		->column(col_def('provincia_id')->t_external_id()->not_null())
		->foreign_key(fk_def('fk_provincia_id')->ref_table('provincia','id')->ref_columns('provincia_id'))
		->go($db);

		drop_table('specie_albero')->if_exists()
		->go($db);

		create_table('specie_albero')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('nome')->t_text256()->not_null())
		->go($db);

		drop_table('albero')->if_exists()
		->go($db);

		create_table('albero')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('data_piantumazione')->t_date())
		->column(col_def('latitudine')->t_float())
		->column(col_def('longitudine')->t_float())
		->column(col_def('specie_albero_id')->t_external_id())
		->column(col_def('comune_id')->t_external_id())
		->foreign_key(fk_def('fk_specie_albero_id')->ref_table('specie_albero','id')->ref_columns('specie_albero_id'))
		->foreign_key(fk_def('fk_comune_id')->ref_table('comune','id')->ref_columns('comune_id'))
		->go($db);

		drop_table('check_up_albero')->if_exists()
		->go($db);

		create_table('check_up_albero')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('albero_id')->t_external_id())
		->column(col_def('data_check_up')->t_date()->not_null())
		->column(col_def('esito')->t_u_tinyint()->not_null())
		->foreign_key(fk_def('fk_albero_id')->ref_table('albero','id')->ref_columns('albero_id'))
		->go($db);

		drop_table('cura_albero')->if_exists()
		->go($db);

		create_table('cura_albero')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('descrizione')->t_text2048()->not_null())
		->column(col_def('codice')->t_text128()->not_null())
		->go($db);

		drop_table('intervento_cura_albero')->if_exists()
		->go($db);

		create_table('intervento_cura_albero')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('descrizione_aggiuntiva')->t_text1024()->not_null())
		->column(col_def('cura_albero_id')->t_external_id()->not_null())
		->foreign_key(fk_def('fk_cura_albero_id')->ref_table('cura_albero','id')->ref_columns('cura_albero_id'))
		->go($db);

		drop_table('problema_salute_albero')->if_exists()
		->go($db);

		create_table('problema_salute_albero')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('nome')->t_text256()->not_null())
		->column(col_def('descrizione')->t_text1024()->not_null())
		->column(col_def('codice')->t_text32()->not_null())
		->go($db);

		drop_table('problema_check_up')->if_exists()
		->go($db);

		create_table('problema_check_up')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('check_up_albero_id')->t_external_id()->not_null())
		->column(col_def('problema_salute_albero_id')->t_external_id())
		->column(col_def('intervento_cura_albero_id')->t_external_id())
		->go($db);

		foreign_key_checks(true)->go($db);

	}


}