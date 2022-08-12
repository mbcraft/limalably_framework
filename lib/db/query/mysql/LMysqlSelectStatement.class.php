<?php




class LMysqlSelectStatement {
	
	private $distinct_option = "";
	private $field_name_list;
	private $table_name_list;
	private $where_block = "";
	private $join_list = [];
	private $order_by_clause = "";
	private $group_by_clause = "";
	private $with_rollup_option = "";
	private $having_clause = "";
	private $limit_clause = "";

	public function __construct($field_name_list,$table_name_list,$where_block) {

		ensure_instance_of("field name list of mysql select statement",$table_name_list,[LMysqlElementList::class]);
		$this->field_name_list = $field_name_list;	

		ensure_instance_of("table name list of mysql select statement",$table_name_list,[LMysqlElementList::class]);
		$this->table_name_list = $table_name_list;

		if ($where_block!=null) {
			ensure_instance_of("where condition of mysql select statement",$where_block,[LMysqlWhereBlock::class]);
			$this->where_block = $where_block;
		} else {
			$this->where_block = "";
		}
	}

	public function with_distinct() {
		$this->distinct_option = "DISTINCT ";

		return $this;
	}

	private function ensure_valid_order_by_element($order_by_element) {

		if (!is_string($order_by_element)) throw new \Exception("The order by element is not a string in the mysql select clause.");

		$lowered = strtolower($order_by_element);
		$parts = explode(' ',$order_by_element);

		if (count($parts)!=2) throw new \Exception("The order by element is not made of two space separated strings.");
		$order_descriptor = $parts[1];

		if ($order_descriptor!='asc' && $order_descriptor!='desc') throw new \Exception("Order descriptor is neither 'asc' or 'desc' in mysql select order by clause.");
	}	

	public function inner_join($table_name,$condition_element=null) {
		$this->join_list[] = LMysqlGenericJoin::inner_join($table_name,$condition_element);

		return $this;
	}

	public function left_join($table_name,$condition_element=null) {
		$this->join_list[] = LMysqlGenericJoin::left_join($table_name,$condition_element);

		return $this;
	}

	public function right_join($table_name,$condition_element=null) {
$		this->join_list[] = LMysqlGenericJoin::right_join($table_name,$condition_element);

		return $this;
	}

	public function cross_join($table_name,$condition_element=null) {
		$this->join_list[] = LMysqlGenericJoin::cross_join($table_name,$condition_element);

		return $this;
	}


	public function order_by(... $field_list_with_ordering) {
		foreach ($field_list_with_ordering as $order_by_element) {
			$this->ensure_valid_order_by_element($order_by_element);
			$this->order_by_clause = new LMysqlElementList($field_list_with_ordering);
		}

		return $this;
	} 

	public function paginate($page_size,$page_number) {
		if ($page_number==0) throw new \Exception("Page number cannnot be zero, starts from one in mysql select statement limit clause.");
		$limit_start = ($page_number-1)*$page_size;
		$limit_end = ($page_number*$page_size)-1;

		$this->limit_clause = " LIMIT ".$limit_start.",".$limit_end;

		return $this;
	}

	public function group_by(... $field_name_list) {

		$this->group_by_clause = new LMysqlElementList($field_name_list);

		$this->with_rollup_option = "";

		return $this;
	}

	public function group_by_with_rollup(... $field_name_list) {
		$this->group_by_clause = new LMysqlElementList($field_name_list);

		$this->with_rollup_option = " WITH ROLLUP";

		return $this;	
	}

	public function having($element) {

		$this->having_clause = new LMysqlHavingBlock($element);

		return $this;
	}

	public function __toString() {
		return "SELECT ".$this->distinct_option.$this->field_name_list->toRawStringListWithoutParenthesis()." FROM ".$this->table_name_list->toRawStringListWithoutParenthesis()." ".implode(' ',$this->join_list)." ".$this->where_block." ".$this->group_by_clause->toRawStringListWithoutParenthesis().$this->with_rollup_option." ".$this->having_clause." ".$this->order_by_clause->toRawStringListWithoutParenthesis()." ".$this->limit_clause.";";
	}
}