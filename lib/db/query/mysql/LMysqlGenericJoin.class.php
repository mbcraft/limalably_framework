<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlGenericJoin {
	
	private $join_type;
	private $table_name;
	private $on_block = "";

	private function __construct($join_type,$table_name,$on_block=null) {

		$this->join_type = $join_type;

		if (!is_string($table_name)) throw new \Exception("Invalid table name in ".$join_type." clause in mysql select statement.");
		$this->table_name = $table_name;

		if ($on_block!=null) {

			if (is_array($on_block)) {
				if (empty($on_block)) return;
				$on_block_ok = new LMysqlOnBlock(new LMysqlAndBlock(... $on_block));
			}
			if ($on_block instanceof LMysqlElementList) $on_block_ok = new LMysqlOnBlock(new LMysqlAndBlock(... $on_block->getElements()));
			if ($on_block instanceof LMysqlCondition) $on_block_ok = new LMysqlOnBlock($on_block);
			if ($on_block instanceof LMysqlAndBlock) $on_block_ok = new LMysqlOnBlock($on_block);
			if ($on_block instanceof LMysqlOrBlock) $on_block_ok = new LMysqlOnBlock($on_block);
			if ($on_block instanceof LMysqlOnBlock) $on_block_ok = $on_block;

			ensure_instance_of("The on block of the join condition is not a valid element type.",$on_block_ok,[LMysqlOnBlock::class]);

			$this->on_block = $on_block_ok;
		}

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
		return trim(strtoupper($this->join_type).$this->table_name.$this->on_block);
	}

}