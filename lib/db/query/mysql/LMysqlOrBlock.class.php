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

		if ($conditions && count($conditions)==1 && is_array($conditions[0])) $conditions = $conditions[0]; 

		ensure_all_instances_of("mysql statement with 'or' block",$conditions,[LMysqlOrBlock::class,LMysqlAndBlock::class,LMysqlCondition::class]);
		
		$this->conditions = $conditions;
	}

	public function add($condition) {
		ensure_instance_of("mysql statement with 'and' block",$condition,[LMysqlOrBlock::class,LMysqlAndBlock::class,LMysqlCondition::class]);

		$this->conditions[] = $condition;

		return $this;
	}

	public function __toString() {
		return "( ".implode(' OR ',$this->conditions)." )";
	}
}