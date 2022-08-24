<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlInsertStatement extends LMysqlAbstractInsertOrReplaceStatement
{

	private $ignore_option = "";
	private $on_duplicate_key_update_option = "";
	
	protected function statement_name() {
		return "insert";
	}

	public function with_ignore() {
		$this->ignore_option = "IGNORE";

		return $this;
	}

	public function on_duplicate_key_update(array $name_value_pair_list) {

		$name_value_pair_list_obj = new LMysqlNameValuePairList($name_value_pair_list);

		$this->on_duplicate_key_update_option = "ON DUPLICATE KEY UPDATE ".$name_value_pair_list_obj;

		return $this;
	}

	public function __toString() {

		if (!$this->column_list) throw new \Exception("Column list is not defined in mysql insert statement");
		if (!$this->data) throw new \Exception("Data is not defined in mysql insert statement");

		return $this->build_query("INSERT",$this->ignore_option,"INTO",$this->table_name,$this->column_list->toRawStringList(),$this->insert_data_connector,
			$this->data,$this->on_duplicate_key_update_option);

	}
}