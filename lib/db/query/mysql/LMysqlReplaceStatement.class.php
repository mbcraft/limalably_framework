<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlReplaceStatement  extends LMysqlAbstractQuery {

	private $table_name;
	private $column_list = "";
	private $select_set_or_values;
	
	private function __construct($table_name,$column_list,$select_set_or_values) {

		if (!is_string($table_name)) throw new \Exception("Table name is not a string in mysql replace statement");
		$this->table_name = $table_name;

		if ($column_list) {
			ensure_instance_of("column list in mysql replace statement",$column_list,[LMysqlElementList::class]);
			$this->column_list = $column_list;
		}

		ensure_instance_of("select, set or values for mysql replace statement",$select_set_or_values,[LMysqlElementList::class,LMysqlElementListList::class,LMysqlNameValuePairList::class,LMysqlSelectStatement::class]);
		$this->select_set_or_values = $select_set_or_values;

	}

	public function __toString() {

		if ($this->select_set_or_values instanceof LMysqlSelectStatement) {
			return "REPLACE INTO ".$this->table_name.$this->column_list->toRawStringList()." ".$this->select_set_or_values;
		}
		if ($this->select_set_or_values instanceof LMysqlNameValuePairList) {
			return "REPLACE INTO ".$this->table_name." SET ".$this->select_set_or_values;
		}

		return "REPLACE INTO ".$this->table_name.$this->column_list->toRawStringList()." VALUES ".$this->select_set_or_values;
	}

}