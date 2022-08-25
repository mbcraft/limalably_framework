<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlGenericJoin {
	
	private $join_type;
	private $table_name;
	private $on_block = "";

	private function __construct($join_type,$table_name,$on_block_or_using=null) {

		$this->join_type = $join_type;

		if (!is_string($table_name)) throw new \Exception("Invalid table name in ".$join_type." clause in mysql select statement.");
		$this->table_name = $table_name;

		if ($on_block_or_using!=null) {

			if (is_array($on_block_or_using)) {
				if (empty($on_block_or_using)) return;
				$on_block_ok = new LMysqlOnBlock(new LMysqlAndBlock(... $on_block_or_using));
			}
			if ($on_block_or_using instanceof LMysqlElementList) $on_block_ok = new LMysqlOnBlock(new LMysqlAndBlock(... $on_block_or_using->getElements()));
			if ($on_block_or_using instanceof LMysqlCondition) $on_block_ok = new LMysqlOnBlock($on_block_or_using);
			if ($on_block_or_using instanceof LMysqlAndBlock) $on_block_ok = new LMysqlOnBlock($on_block_or_using);
			if ($on_block_or_using instanceof LMysqlOrBlock) $on_block_ok = new LMysqlOnBlock($on_block_or_using);
			if ($on_block_or_using instanceof LMysqlOnBlock) $on_block_ok = $on_block_or_using;
			if (is_string($on_block_or_using)) $on_block_ok = "USING (".$on_block_or_using.")";

			$this->on_block = $on_block_ok;
		}

	}

	public static function inner_join($table_name,$condition_element_or_using=null) {
		return new LMysqlGenericJoin('inner join ',$table_name,$condition_element_or_using);
	}

	public static function left_join($table_name,$condition_element_or_using=null) {
		return new LMysqlGenericJoin('left join ',$table_name,$condition_element_or_using);
	}

	public static function right_join($table_name,$condition_element_or_using=null) {
		return new LMysqlGenericJoin('right join ',$table_name,$condition_element_or_using);
	}

	public static function cross_join($table_name,$condition_element_or_using=null) {
		return new LMysqlGenericJoin('cross join ',$table_name,$condition_element_or_using);
	}

	public function __toString() {
		return trim(strtoupper($this->join_type).$this->table_name.$this->on_block);
	}

}