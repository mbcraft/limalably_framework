<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlFunctions {
	

	private $parts = [];

	private function __construct(... $parts) {
		$this->parts = $parts;
	}

	public function __toString() {
		return implode(" ",$this->parts);
	}

	public static function ifnull(string $column_name,$column_value) {
		return new LMysqlFunctions('IFNULL','(',new LMysqlColumnName($column_name),new LMysqlValueRenderer($column_value),')');
	}


}