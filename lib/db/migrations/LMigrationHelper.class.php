<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LMigrationHelper {
	
	const MIGRATIONS_TABLE_NAME = "migrations_log";

	const MIGRATIONS_NAME_COLUMN = "name";

	const MIGRATIONS_CONTEXT_COLUMN = "context";

	const MIGRATIONS_EXECUTION_TIME_COLUMN = "executed_at";

	public static function migrationTableExists() {

		$db = db();

		$table_list = table_list()->go($db);

		return in_array(self::MIGRATIONS_TABLE_NAME,$table_list);
	}

	public static function ensureMigrationTableExist() {

		$db = db();

		if (self::migrationTableExists()) return;

		create_table(self::MIGRATIONS_TABLE_NAME)
		->column(col_def(self::MIGRATIONS_NAME_COLUMN)->t_text128())
		->column(col_def(self::MIGRATIONS_CONTEXT_COLUMN)->t_text64())
		->column(col_def(self::MIGRATIONS_EXECUTION_TIME_COLUMN)->t_datetime()->default_value(_expr('NOW()')))
		->go($db);

	}

	public static function dropMigrationTable() {

		$db = db();
	
		drop_table(self::MIGRATIONS_TABLE_NAME)->if_exists()->go($db);

	}

	public static function listMigrations() {

		$db = db();

		return select('*',self::MIGRATIONS_TABLE_NAME)->go($db);

	}

	public static function isMigrationExecuted($name,$context) {

		$db = db();

		$result = select('count(*) AS C',self::MIGRATIONS_TABLE_NAME)->where(_and(_eq(self::MIGRATIONS_NAME_COLUMN,$name)),_eq(self::MIGRATIONS_CONTEXT_COLUMN,$context))->go($db);

		return $result[0]['C']==1;
	}

	public static function getMigrationExecutionTime($name,$context) {
		
		$db = db();

		$result = select('*',self::MIGRATIONS_TABLE_NAME)->where(_and(_eq(self::MIGRATIONS_NAME_COLUMN,$name)),_eq(self::MIGRATIONS_CONTEXT_COLUMN,$context))->go($db);

		if (count($result)!=1) return false;
		else return $result[0][self::MIGRATIONS_EXECUTION_TIME_COLUMN];
	}

	public static function logMigration($name,$context) {

		$db = db();

		insert(self::MIGRATIONS_TABLE_NAME,[self::MIGRATIONS_NAME_COLUMN,self::MIGRATIONS_CONTEXT_COLUMN],[$name,$context])->go($db);

	}

	public static function removeMigrationLog($name,$context) {
		$db = db();

		delete(self::MIGRATIONS_TABLE_NAME)->where(_and(_eq(self::MIGRATIONS_NAME_COLUMN,$name)),_eq(self::MIGRATIONS_CONTEXT_COLUMN,$context))->go($db);
	}

	public static function getCleanContextName($context) {
		if (LStringUtils::endsWith($context,'/')) return "[".substr($context,0,-1)."]";
		else return "[".$context."]";
	}

}