<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/


class LMysqlCreateTableStatement extends LMysqlAbstractQuery {
	
	private $table_name;
	private $temporary_modifier = "";
	private $if_not_exists_option = "";
	private $col_defs = [];
	private $primary_key = null;
	private $unique_constraints = [];
	private $foreign_keys = [];
	private $engine = "MyISAM";
	private $charset_trailer = "";

	function __construct($table_name)
	{
		$this->table_name = $table_name;

	}

	function charset($charset_name) {

		$this->charset_trailer = "DEFAULT CHARSET=".$charset_name;

		return $this;
	}

	function temporary() {
		$this->temporary_modifier = "TEMPORARY";

		return $this;
	}

	function if_not_exists() {
		$this->if_not_exists = "IF NOT EXISTS";

		return $this;
	}

	function column($column_definition) {
		if (!$column_definition instanceof LMysqlColumnDefinition) throw new \Exception("The parameter is not a valid column definition! Use column_def function to create column definitions.");

		$this->col_defs[] = $column_definition;

		return $this;
	}

	function primary_key(... $column_names) {
		if (count($column_names)==1 && is_array($column_names)) $cols = $column_names[0];
		else $cols = $column_names;

		$this->primary_key = " PRIMARY KEY ( ".implode(',',$cols)." )";

		return $this;
	}

	function unique($constraint_name,$column_name_list) {

		if (is_string($column_name_list)) $column_name_list = array($column_name_list);
		if (empty($column_name_list)) throw new \Exception("Column name list can't be empty in unique in mysql create table statement");

		$this->unique_constraints[] = " CONSTRAINT ".$constraint_name." UNIQUE ( ".implode(',',$column_name_list)." )";

		return $this;
	}

	function foreign_key($fk_definition) {
		if (!$fk_definition instanceof LMysqlForeignKeyConstraintDefinition) throw new \Exception("The parameter is not a foreign key definition in mysql create table statement");

		$this->foreign_keys[] = $fk_definition;

		return $this;
	}

	function engine_innodb() {
		$this->engine = "InnoDB";

		return $this;
	}

	function engine_myisam() {
		$this->engine = "MyISAM";

		return $this;
	}

	function engine_memory() {
		$this->engine = "MEMORY";

		return $this;
	}

	function engine_archive() {
		$this->engine = "ARCHIVE";

		return $this;
	}

	function engine_csv() {
		$this->engine = "CSV";

		return $this;
	}	

	function __toString() {

		if (empty($this->col_defs)) throw new \Exception("At least one column definition is needed");

		$pk_and_fks = array_merge($this->unique_constraints,$this->foreign_keys);
		if ($this->primary_key) {
			array_unshift($pk_and_fks,$this->primary_key);
		}

		$elements = array_merge($this->col_defs,$pk_and_fks);

		return $this->build_query("CREATE",$this->temporary_modifier,"TABLE",$this->if_not_exists_option,$this->table_name,"(",implode(",",$elements),")","ENGINE","=",$this->engine,$this->charset_trailer);

	}


}