<?php


class LMysqlAlterTableStatement extends LMysqlAbstractQuery {
	
	private $table_name;
	private $changes = [];

	function __construct($table_name) {


		$this->table_name = $table_name;

	}

	function drop_foreign_key($constraint_name) {
		if (!is_string($constraint_name)) throw new \Exception("Constraint name to drop is not a string.");

		$this->changes[] = "DROP FOREIGN KEY ".$constraint_name;

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
	}

	function __toString() {

		if (empty($this->changes)) throw new \Exception("No table changes are declared. Declare at least one change to do.");

		return $this->build_query("ALTER TABLE",$this->table_name,implode(',',$this->changes));

	}

}