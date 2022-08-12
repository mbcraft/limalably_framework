<?php

class LMysqlInsertStatement extends LMysqlAbstractCrudStatement
{

	private $ignore_option = "";
	private $table_name;
	private $column_list;
	private $insert_data_connector;
	private $data;
	
	public function __construct($table_name,$column_list,$data) {

		if (!is_string($table_name)) throw new \Exception("Table name is not a valid string. ".$table_name." found.");
		$this->table_name = $table_name;
		
		ensure_instance_of("mysql column list in insert statement",$column_list,[LMysqlElementList::class]);
		$this->column_list = $column_list;
		
		if ($data instanceof LMysqlSelectStatement)
			$this->insert_data_connector = "";
		else
			$this->insert_data_connector = " VALUES ";
		ensure_instance_of("mysql data of insert statement",$data,[LMysqlSelectStatement::class,LMysqlElementList::class,LMysqlElementListList::class]);
		$this->data = $data;
	}

	public function with_ignore() {
		$this->ignore_option = " IGNORE";

		return $this;
	}

	public function __toString() {
		return "INSERT".$this->ignore_option." INTO ".$this->table_name.$this->column_list->toRawStringList().$this->insert_data_connector.$this->data.";";
	}
}