<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/


class LMysqlAlterTableStatement extends LMysqlAbstractQuery {
	
	private $table_name;
	private $changes = [];

	function __construct($table_name) {


		$this->table_name = $table_name;

	}

	function add_primary_key(... $column_names) {
		if (count($column_names)==1 && is_array($column_names)) $cols = $column_names[0];
		else $cols = $column_names;

		$this->changes[] = "ADD PRIMARY KEY ( ".implode(',',$cols)." )";

		return $this;
	}

	function drop_foreign_key($constraint_name) {
		if (!is_string($constraint_name)) throw new \Exception("Constraint name to drop is not a string.");

		$this->changes[] = "DROP FOREIGN KEY ".$constraint_name;

		return $this;

	}

	function drop_unique_index($constraint_name) {
		if (!is_string($constraint_name)) throw new \Exception("Constraint name to drop is not a string.");

		$this->changes[] = "DROP INDEX ".$constraint_name;

		return $this;

	}

	function drop_column($column_name) {

		if (!is_string($column_name)) throw new \Exception("Column name to drop is not a string.");

		$this->changes[] = "DROP COLUMN ".$column_name;

		return $this;
	}

	function modify_column($column_definition) {
		if (!$column_definition instanceof LMysqlColumnDefinition) throw new \Exception("Column definition is not valid. Use col_def function to create column definitions.");

		$this->changes[] = "MODIFY ".$column_definition;

		return $this;
	}

	function change_column($old_column_name,$column_definition) {
		if (!is_string($old_column_name)) throw new \Exception("Old column name is not a string.");
		if (!$column_definition instanceof LMysqlColumnDefinition) throw new \Exception("Column definition is not valid. Use col_def function to create column definitions.");

		$this->changes[] = "CHANGE COLUMN ".$old_column_name." ".$column_definition;

		return $this;
	}

	function add_column($column_definition) {

		if (!$column_definition instanceof LMysqlColumnDefinition) throw new \Exception("Column definition is not valid. Use col_def function to create column definitions.");

		$this->changes[] = "ADD ".$column_definition;

		return $this;
	}

	function add_foreign_key($fk_definition) {

		if (!$fk_definition instanceof LMysqlForeignKeyConstraintDefinition) throw new \Exception("parameter is not a valid foreign key definition in mysql alter table statement");

		$this->changes[] = "ADD ".$fk_definition;

		return $this;
	}

	function add_unique_index($constraint_name,$column_name_list) {

		if (is_string($column_name_list)) $column_name_list = array($column_name_list);
		if (empty($column_name_list)) throw new \Exception("Column name list can't be empty in add_unique_index in mysql alter table statement");

		$this->changes[] = "ADD CONSTRAINT ".$constraint_name." UNIQUE ( ".implode($column_name_list)." ) ";

		return $this;
	}

	function __toString() {

		if (empty($this->changes)) throw new \Exception("No table changes are declared. Declare at least one change to do.");

		return $this->build_query("ALTER TABLE",$this->table_name,implode(',',$this->changes));

	}

}