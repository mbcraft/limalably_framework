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
	
	public function __construct($table_name,$column_list=null,$data=null) {

		if (!is_string($table_name)) throw new \Exception("Table name is not a valid string. ".$table_name." found.");
		$this->table_name = $table_name;
		
		if ($column_list!==null) {
			if (is_array($column_list)) {
				if (empty($column_list)) throw new \Exception("The column list of the mysql insert statement is empty");
				$column_list = new LMysqlElementList(... $column_list);
			}
			if (is_string($column_list)) $column_list = new LMysqlElementList([$column_list]);
			
			ensure_instance_of("mysql column list in insert statement",$column_list,[LMysqlElementList::class]);
			
			$this->column_list = $column_list;
		}

		if ($data) {
			$this->data($data);
		}
	}

	public function column_list(... $column_list) {

		if (count($column_list)>1) {
			$this->column_list = new LMysqlElementList($column_list);

			return $this;
		}

		if (count($column_list)==1) {
			if (is_string($column_list[0])) {
				$this->column_list = new LMysqlElementList([$column_list[0]]);

				return $this;	
			} else {
				$this->column_list = new LMysqlElementList(... $column_list[0]);

				return $this;
			}
		}

		throw new \Exception("Invalid column list in mysql insert statement");
	}

	public function data($data) {

		if ($data instanceof LMysqlSelectStatement) {
			$this->insert_data_connector = "";

			$my_data = $data;
		}
		else
			$this->insert_data_connector = "VALUES";

		if (is_array($data))
		{
			if (empty($data)) throw new \Exception("data array is empty in mysql insert statement");
			if(is_array($data[0])) 
			{
				$my_data = new LMysqlElementListList(... $data);
			} else {
				$my_data = new LMysqlElementList(... $data);
			}
		}

		ensure_instance_of("mysql data of insert statement",$my_data,[LMysqlSelectStatement::class,LMysqlElementList::class,LMysqlElementListList::class]);
		$this->data = $my_data;

		return $this;
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