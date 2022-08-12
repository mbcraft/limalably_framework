<?php

/**
 * 
 */
class LMysqlAndBlock
{
	private $conditions;

	public fuction __construct(... $conditions) {
		
		ensure_all_instances_of("mysql statement with 'and' block",$conditions,[LMysqlOrBlock::class,LMysqlAndBlock::class,LMysqlCondition::class]);	

		$this->conditions = $conditions;
	}

	public function __toString() {
		return " ( ".implode(' AND ',$this->conditions)." ) ";
	}
}