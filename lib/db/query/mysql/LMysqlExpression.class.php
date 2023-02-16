<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlExpression {
	

	private $expression;

	function __construct(string $expression) {

		$this->expression = $expression;

	}

	function __toString() {
		return $this->expression;
	}


}