<?php

class LMysqlInsertStatement
{

	private $table_name;
	private $column_list;
	private $data;
	
	function __construct($table_name,$column_list,$data) {

		if (!is_string($table_name)) throw new \Exception("Table name is not a valid string. ".$table_name." found.");

		ensure_instance_of("mysql column list in insert statement",$column_list,[LMysqlElementList::class]);
		
		ensure_instance_of("mysql data of insert statement",$data,[LMysqlElementList::class,LMysqlElementListList::class]);
		
		$this->table_name = $table_name;
		$this->column_list = $column_list;
		$this->data = $data;
	}

	function __toString() {
		return "INSERT INTO ".$this->table_name.$this->column_list." VALUES ".$this->data.";";
	}
}