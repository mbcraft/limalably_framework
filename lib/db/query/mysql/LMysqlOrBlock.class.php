<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlOrBlock
{
	private $conditions;

	public function __construct(... $conditions) {
		ensure_all_instances_of("mysql statement with 'or' block",$conditions,[LMysqlOrBlock::class,LMysqlAndBlock::class,LMysqlCondition::class]);
		
		$this->conditions = $conditions;
	}

	public function __toString() {
		return "( ".implode(' OR ',$this->conditions)." )";
	}
}