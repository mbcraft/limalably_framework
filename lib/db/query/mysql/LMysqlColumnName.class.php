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
	
	private $result;

	function __construct(string $column_name,string $column_alias = null) {
		$this->result = $column_name;
		if ($column_alias) $this->result .= " AS ".$column_alias;
	}

	function __toString() {
		return $this->result;
	}
}