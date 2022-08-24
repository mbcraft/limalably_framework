<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlColumnName {
	
	private $column_name;

	function __construct($column_name) {
		$this->column_name = $column_name;
	}

	function __toString() {
		return $this->column_name;
	}
}