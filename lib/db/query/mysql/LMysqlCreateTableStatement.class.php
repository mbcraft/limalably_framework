<?php


class LMysqlCreateTableStatement extends LMysqlAbstractQuery {
	
	private $table_name;
	private $if_not_exists_option = "";
	private $column_definitions = [];
	private $engine = "InnoDB";

	function __construct($table_name)
	{
		$this->table_name = $table_name;

	}

	function if_not_exists() {
		$this->if_not_exists = "IF NOT EXISTS";

		return $this;
	}

	function column($column_definition) {
		if (!$column_definition instanceof LMysqlColumnDefinition) throw new \Exception("The parameter is not a valid column definition! Use column_def function to create column definitions.");

		$this->column_definitions[] = $column_definition;

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

		if (empty($this->column_definitions)) throw new \Exception("At least one column definition is needed");

		return $this->build_query("CREATE TABLE",$this->if_not_exists_option,$this->table_name,"(",implode(",",$this->column_definitions),")","ENGINE","=",$this->engine);

	}


}