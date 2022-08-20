<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlDeleteStatement extends LMysqlAbstractQuery
{
	private $table_name;
	private $join_table_list = [];
	private $join_list = [];
	private $where_condition;
	private $order_by_clause = "";
	private $limit = "";

	public function __construct($table_name,$where_block=null) {
		
		if (!is_string($table_name)) throw new \Exception("The table name of the update statement is not a string.");
		$this->join_table_list[] = $table_name;
		$this->table_name = $table_name;

		if ($where_block!=null) {

			if (!$where_block instanceof LMysqlWhereBlock) $where_block = new LMysqlWhereBlock($where_block);

			ensure_instance_of("where condition of mysql update statement",$where_block,[LMysqlWhereBlock::class]);
			
			$this->where_block = $where_block;
		} else {
			$this->where_block = "";
		}
	}

	public function where(... $elements) {

		if (count($elements)==1 && is_array($elements[0])) {
			$this->where_block = new LMysqlWhereBlock($elements[0]);
		} else {
			$this->where_block = new LMysqlWhereBlock($elements);
		}
		
		return $this;
	}

	public function inner_join(bool $include,$table_name,$condition_element=null) {

		if ($include) {
			$this->join_table_list[] = $table_name;
		}

		$this->join_list[] = LMysqlGenericJoin::inner_join($table_name,$condition_element);

		return $this;
	}

	public function left_join(bool $include,$table_name,$condition_element=null) {

		if ($include) {
			$this->join_table_list[] = $table_name;
		}

		$this->join_list[] = LMysqlGenericJoin::left_join($table_name,$condition_element);

		return $this;
	}

	public function order_by(... $field_list_with_ordering) {
		foreach ($field_list_with_ordering as $order_by_element) {
			$this->ensure_valid_order_by_element($order_by_element);	
		}

		$order_by_elements = new LMysqlElementList($field_list_with_ordering);

		$this->order_by_clause = "ORDER BY ".$order_by_elements->toRawStringListWithoutParenthesis();

		return $this;
	} 

	public function limit(int $num_rows) {

		$this->limit = "LIMIT ".$num_rows;

		return $this;
	}

	public function __toString() {

		if (empty($this->join_list)) {
			$join_table_list_obj = new LMysqlEmptyElementList();
		} else {
			$join_table_list_obj = new LMysqlElementList($this->join_table_list);
		}

		if ($this->order_by_clause==null && $this->limit!=null) throw new \Exception("order_by and limit must be used both or none in mysql delete statement.");
		if ($this->order_by_clause!=null && $this->limit==null) throw new \Exception("order_by and limit must be used both or none in mysql delete statement.");

		return $this->build_query("DELETE",$join_table_list_obj->toRawStringListWithoutParenthesis(),"FROM",$this->table_name,implode(' ',$this->join_list),$this->where_block,$this->order_by_clause,$this->limit);

	}
	
}