<?php


class LMysqlDeleteStatement
{
	private $table_name;
	private $join_table_list = [];
	private $join_list = [];
	private $where_condition;

	public function __construct($table_name,$where_block) {
		
		if (!is_string($table_name)) throw new \Exception("The table name of the update statement is not a string.");
		$this->join_table_list[] = $table_name;
		$this->table_name = $table_name;

		if ($where_block!=null) {
			ensure_instance_of("where condition of mysql update statement",$where_block,[LMysqlWhereBlock::class]);
			$this->where_block = $where_block;
		} else {
			$this->where_block = "";
		}
	}

	public function inner_join($table_name,$condition_element=null) {

		$this->join_table_list[] = $table_name;

		$this->join_list[] = LMysqlGenericJoin::inner_join($table_name,$condition_element);

		return $this;
	}

	public function left_join($table_name,$condition_element=null) {

		$this->join_table_list[] = $table_name;

		$this->join_list[] = LMysqlGenericJoin::left_join($table_name,$condition_element);

		return $this;
	}

	public function __toString() {

		if (empty($this->join_list)) {
			$join_table_list_obj = new LMysqlElementList();
		} else {
			$join_table_list_obj = new LMysqlElementList($this->join_table_list);
		}

		return "DELETE ".$join_table_list_obj->toRawStringListWithoutParenthesis()." FROM ".$this->table_name." ".implode(' ',$this->join_list)." "$this->where_block.";";
	}
	
}