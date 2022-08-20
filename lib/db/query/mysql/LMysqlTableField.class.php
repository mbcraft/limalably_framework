<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlTableField {
	
	private $field_name;

	function __construct($field_name) {
		$this->field_name = $field_name;
	}

	function __toString() {
		return $this->field_name;
	}
}