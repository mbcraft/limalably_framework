<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlInsertStatement extends LMysqlAbstractQuery
{

	private $ignore_option = "";
	private $table_name;
	private $column_list;
	private $insert_data_connector;
	private $data;
	private $on_duplicate_key_update_option = "";
	
	public function __construct($table_name,$column_list,$data) {

		if (!is_string($table_name)) throw new \Exception("Table name is not a valid string. ".$table_name." found.");
		$this->table_name = $table_name;
		
		if (is_array($column_list)) $column_list = new LMysqlElementList(... $column_list);
		else ensure_instance_of("mysql column list in insert statement",$column_list,[LMysqlElementList::class]);
		$this->column_list = $column_list;
		
		if ($data instanceof LMysqlSelectStatement)
			$this->insert_data_connector = "";
		else
			$this->insert_data_connector = "VALUES";

		if (is_array($data))
		{
			if(is_array($data[0])) 
			{
				$data = new LMysqlElementListList(... $data);
			} else {
				$data = new LMysqlElementList(... $data);
			}
		}

		ensure_instance_of("mysql data of insert statement",$data,[LMysqlSelectStatement::class,LMysqlElementList::class,LMysqlElementListList::class]);
		$this->data = $data;
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

		return $this->build_query("INSERT",$this->ignore_option,"INTO",$this->table_name,$this->column_list->toRawStringList(),$this->insert_data_connector,
			$this->data,$this->on_duplicate_key_update_option);

	}
}