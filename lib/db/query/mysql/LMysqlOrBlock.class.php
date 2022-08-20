<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

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