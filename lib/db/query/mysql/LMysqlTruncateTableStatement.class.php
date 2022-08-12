<?php


class LMysqlTruncateTableStatment {
	
	private $table_name;

	public function __construct($table_name) {
		if (!is_string($table_name)) throw new \Exception("Invalid string as table name in mysql truncate table statement.");
		$this->table_name = $table_name;
	}

	public function __toString() {
		return "TRUNCATE TABLE ".$this->table_name;
	}

}