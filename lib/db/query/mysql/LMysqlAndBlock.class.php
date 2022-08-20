<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlAndBlock
{
	private $conditions;

	public function __construct(... $conditions) {
		
		ensure_all_instances_of("mysql statement with 'and' block",$conditions,[LMysqlOrBlock::class,LMysqlAndBlock::class,LMysqlCondition::class]);	

		$this->conditions = $conditions;
	}

	public function __toString() {
		if (count($this->conditions)>1) {
			return "( ".implode(' AND ',$this->conditions)." )";
		} else {
			return "".$this->conditions[0];
		}
	}
}