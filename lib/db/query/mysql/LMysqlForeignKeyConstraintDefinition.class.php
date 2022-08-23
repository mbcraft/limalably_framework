<?php


class LMysqlForeignKeyConstraintDefinition {
	
	private $constraint_name;

	private $key_columns = null;
	private $ref_table = null;
	private $ref_columns = null;
	private $on_delete = null;
	private $on_update = null;

	function __construct($constraint_name) {
		$this->constraint_name = $constraint_name;
	}

	function key_columns(... $column_names) {

		return $this;
	}

	function ref_table($table_name,$key_columns = null) {

		return $this;
	}

	function ref_columns(... $column_names) {

		return $this;
	}

	function on_delete_cascade() {
		$this->on_delete = "CASCADE";

		return $this;
	}

	function on_delete_set_null() {
		$this->on_delete = "SET NULL";

		return $this;
	}

	function on_delete_restrict() {
		$this->on_delete = "RESTRICT";

		return $this;
	}

	function on_delete_set_default() {
		$this->on_delete = "SET DEFAULT";

		return $this;
	}

	function on_update_cascade() {
		$this->on_update = "CASCADE";

		return $this;
	}

	function on_update_set_null() {
		$this->on_update = "SET NULL";

		return $this;
	}

	function on_update_restrict() {
		$this->on_update = "RESTRICT";

		return $this;
	}

	function on_update_set_default() {
		$this->on_update = "SET DEFAULT";

		return $this;
	}

	function __toString() {
		if (!$this->on_delete) throw new \Exception("on delete behaviour not defined!");
		if (!$this->on_update) throw new \Exception("on update behaviour not defined!");
			
	}

}