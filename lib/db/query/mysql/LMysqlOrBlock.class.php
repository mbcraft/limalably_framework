<?php


class LMysqlOrBlock
{
	private $conditions;

	fuction __construct(... $conditions) {
		ensure_all_instances_of("mysql statement with 'or' block",$conditions,[LMysqlOrBlock::class,LMysqlAndBlock::class,LMysqlWhereCondition::class]);
		
		$this->conditions = $conditions;
	}

	function __toString() {
		return " ( ".implode(' OR ',$this->conditions)." ) ";
	}
}