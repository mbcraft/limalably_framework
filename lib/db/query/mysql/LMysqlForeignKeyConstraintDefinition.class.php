<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/


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

		if (count($column_names)==0) throw new \Exception("At least one key column is required.");
		if (count($column_names)==1 && is_array($column_names[0])) {
			$this->key_columns = $column_names[0];

			return $this;
		}

		$this->key_columns = $column_names;

		return $this;
	}

	function ref_table($table_name,$key_columns = null) {

		$this->ref_table = $table_name;

		if ($key_columns) {
			if (is_string($key_columns)) 
				$this->key_columns = [$key_columns];
			if (is_array($key_columns))
				$this->key_columns = [$key_columns];
		}

		return $this;
	}

	function ref_columns(... $column_names) {

		if (count($column_names)==0) throw new \Exception("At least one key column is required.");
		if (count($column_names)==1 && is_array($column_names[0])) {
			$this->ref_columns = $column_names[0];

			return $this;
		}

		$this->ref_columns = $column_names;

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

		if (!$this->key_columns) throw new \Exception("key columns are not defined in foreign key ".$this->constraint_name);
		if (!$this->ref_table) throw new \Exception("reference table is not defined in foreign key ".$this->constraint_name);
		if (!$this->ref_columns) throw new \Exception("referenced table columns are not defined in foreign key ".$this->constraint_name);

		$key_cols_list = new LMysqlElementList($this->key_columns);
		$ref_cols_list = new LMysqlElementList($this->ref_columns);


		$result = "CONSTRAINT ".$this->constraint_name." FOREIGN KEY ".$key_cols_list->toRawStringList()." REFERENCES ".$this->ref_table.$ref_cols_list->toRawStringList();
		if ($this->on_delete)
			$result.= " ON DELETE ".$this->on_delete;
		if ($this->on_update)
			$result.= " ON UPDATE ".$this->on_update;

		return $result;			
	}

}