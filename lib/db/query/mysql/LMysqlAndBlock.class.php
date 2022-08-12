<?php

/**
 * 
 */
class LMysqlAndBlock
{
	private $conditions;

	fuction __construct(... $conditions) {
		
		ensure_all_instances_of("mysql statement with 'and' block",$conditions,[LMysqlOrBlock::class,LMysqlAndBlock::class,LMysqlCondition::class]);	

		$this->conditions = $conditions;
	}

	function __toString() {
		return " ( ".implode(' AND ',$this->conditions)." ) ";
	}
}