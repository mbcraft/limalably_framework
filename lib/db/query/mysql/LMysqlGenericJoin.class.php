<?php


class LMysqlGenericJoin {
	
	private $join_type;
	private $table_name;
	private $on_block = "";

	private function __construct($join_type,$table_name,$condition_element=null) {

		$this->join_type = $join_type;

		if (!is_string($table_name)) throw new \Exception("Invalid table name in ".$join_type." clause in mysql select statement.");
		$this->table_name = $table_name;

		if ($condition_element!=null)
			$this->on_block = new LMysqlOnBlock($condition_element);

	}

	public static function inner_join($table_name,$condition_element=null) {
		return new LMysqlGenericJoin('inner join ',$table_name,$condition_element);
	}

	public static function left_join($table_name,$condition_element=null) {
		return new LMysqlGenericJoin('left join ',$table_name,$condition_element);
	}

	public static function right_join($table_name,$condition_element=null) {
		return new LMysqlGenericJoin('right join ',$table_name,$condition_element);
	}

	public static function cross_join($table_name,$condition_element=null) {
		return new LMysqlGenericJoin('cross join ',$table_name,$condition_element);
	}

	public function __toString() {
		return strtoupper($this->join_mode).$this->table_name." ".$this->on_block." ";
	}

}