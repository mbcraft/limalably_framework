<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlReplaceValue {
	

	private $column_name;
	private $search_value;
	private $replace_value;

	function __construct($column_name,$search_value,$replace_value) {
		if (!is_string($column_name)) throw new \Exception("Field name is not a string in replace element");
		if (!is_string($search_value)) throw new \Exception("Search value is not a string in replace element");
		if (!is_string($replace_value)) throw new \Exception("Replace value is not a string in replace element");

		$this->column_name = $column_name;
		$this->search_value = $search_value;
		$this->replace_value = $replace_value;
	}

	function __toString() {
		return "REPLACE(".$this->column_name.",'".mysqli_real_escape_string(LDbConnectionManager::getLastConnectionUsed()->getHandle(),$this->search_value)."','".mysqli_real_escape_string(LDbConnectionManager::getLastConnectionUsed()->getHandle(),$this->replace_value)."')";
	}


}