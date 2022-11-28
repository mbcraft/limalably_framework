<?php



class LDbUtils {
	

	public static function deleteAllTables() {

		$db = db();

		foreign_key_checks(false)->go($db);

		$table_list = table_list()->go($db);

        foreach ($table_list as $tb) {
            drop_table($tb)->go($db);
        }

        foreign_key_checks(true)->go($db);
	}

}