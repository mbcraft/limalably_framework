<?php


class LMysqlDropColumnStatement extends LMysqlAbstractQuery {
	
	private $table_name;
	private $columns = [];

	function __construct($table_name) {


		$this->table_name = $table_name;

	}

	function column($column_name) {

		$this->columns[] = "DROP COLUMN " $column_name;

		return $this;
	}

	function __toString() {

		if (empty($this->columns)) throw new \Exception("No column to drop are declared. Declare at least one column to drop.");

		return $this->build_query("ALTER TABLE",$this->table_name,implode(',',$this->columns));

	}

}