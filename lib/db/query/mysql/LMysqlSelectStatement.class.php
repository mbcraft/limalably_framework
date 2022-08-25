<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlSelectStatement extends LMysqlAbstractQuery {
	
	private $distinct_option = "";
	private $column_name_list;
	private $table_name_list;
	private $where_block = "";
	private $join_list = [];
	private $order_by_clause;
	private $group_by_prefix = "";
	private $group_by_clause;
	private $with_rollup_option = "";
	private $having_clause = "";
	private $limit_clause = "";
	private $export_to_csv_def = null;

	public function __construct($column_name_list,$table_name_list,$where_block=null) {


		if (is_string($column_name_list) || is_object($column_name_list)) $fnl = new LMysqlElementList($column_name_list);
		if (is_array($column_name_list)) $fnl = new LMysqlElementList(... $column_name_list);
		if ($column_name_list instanceof LMysqlElementList) $fnl = $column_name_list;
		ensure_instance_of("field name list of mysql select statement",$fnl,[LMysqlElementList::class]);
		$this->column_name_list = $fnl;

		if ($table_name_list instanceof LMysqlTableName) $tnl = new LMysqlElementList([$table_name_list]);
		if (is_string($table_name_list)) $tnl = new LMysqlElementList($table_name_list);
		if (is_array($table_name_list)) $tnl = new LMysqlElementList(... $table_name_list);
		if ($table_name_list instanceof LMysqlElementList) $tnl = $table_name_list;
		ensure_instance_of("table name list of mysql select statement",$tnl,[LMysqlElementList::class]);
		$this->table_name_list = $tnl;

		if ($where_block!=null) {

			if (!$where_block instanceof LMysqlWhereBlock) $where_block = new LMysqlWhereBlock($where_block);

			ensure_instance_of("where condition of mysql select statement",$where_block,[LMysqlWhereBlock::class]);
			
			$this->where_block = $where_block;
		} else {
			$this->where_block = "";
		}

		$this->order_by_clause = new LMysqlEmptyElementList();
		$this->group_by_clause = new LMysqlEmptyElementList();
	}

	public function where(... $elements) {

		if (count($elements)==1 && is_array($elements[0])) {
			$this->where_block = new LMysqlWhereBlock($elements[0]);
		} else {
			$this->where_block = new LMysqlWhereBlock($elements);
		}
		
		return $this;
	}

	public function with_distinct() {
		$this->distinct_option = "DISTINCT ";

		return $this;
	}	

	public function inner_join($table_name,$condition_element_or_using=null) {
		$this->join_list[] = LMysqlGenericJoin::inner_join($table_name,$condition_element_or_using);

		return $this;
	}

	public function left_join($table_name,$condition_element_or_using=null) {
		$this->join_list[] = LMysqlGenericJoin::left_join($table_name,$condition_element_or_using);

		return $this;
	}

	public function right_join($table_name,$condition_element_or_using=null) {
		$this->join_list[] = LMysqlGenericJoin::right_join($table_name,$condition_element_or_using);

		return $this;
	}

	public function cross_join($table_name,$condition_element_or_using=null) {
		$this->join_list[] = LMysqlGenericJoin::cross_join($table_name,$condition_element_or_using);

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

	public function paginate($page_size,$page_number) {
		if ($page_number==0) throw new \Exception("Page number cannnot be zero, starts from one in mysql select statement limit clause.");
		$limit_start = ($page_number-1)*$page_size;
		$limit_end = ($page_number*$page_size);

		$this->limit_clause = "LIMIT ".$limit_start.",".$limit_end;

		return $this;
	}

	public function group_by(... $column_name_list) {


		$this->group_by_prefix = "GROUP BY";

		$this->group_by_clause = new LMysqlElementList($column_name_list);

		$this->with_rollup_option = "";

		return $this;
	}

	public function group_by_with_rollup(... $column_name_list) {
		$this->group_by_clause = new LMysqlElementList($column_name_list);

		$this->with_rollup_option = " WITH ROLLUP";

		return $this;	
	}

	public function having($element) {

		$this->having_clause = new LMysqlHavingBlock($element);

		return $this;
	}

	public function export_to_csv($csv_def) {

		if (!$csv_def instanceof LMysqlCsvDefinition) throw new \Exception("Csv definition is not valid in mysql select statement");

		$this->export_to_csv_def = $csv_def;

		return $this;
	}

	public function __toString() {

		$export_to_csv_trailer = "";

		if ($this->export_to_csv_def) {
			$export_to_csv_trailer = $this->export_to_csv_def->__write_header()." ".$this->export_to_csv_def->__trailer();
		}

		return $this->build_query("SELECT",$this->distinct_option,$this->column_name_list->toRawStringListWithoutParenthesis(),
			"FROM",$this->table_name_list->toRawStringListWithoutParenthesis(),implode(' ',$this->join_list),$this->where_block,
			$this->group_by_prefix,$this->group_by_clause->toRawStringListWithoutParenthesis(),$this->with_rollup_option,$this->having_clause,
			$this->order_by_clause,$this->limit_clause,$export_to_csv_trailer);

	}
}