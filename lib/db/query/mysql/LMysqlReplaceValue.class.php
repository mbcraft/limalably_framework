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
	

	private $field_name;
	private $search_value;
	private $replace_value;

	function __construct($field_name,$search_value,$replace_value) {
		if (!is_string($field_name)) throw new \Exception("Field name is not a string in replace element");
		if (!is_string($search_value)) throw new \Exception("Search value is not a string in replace element");
		if (!is_string($replace_value)) throw new \Exception("Replace value is not a string in replace element");

		$this->field_name = $field_name;
		$this->search_value = $search_value;
		$this->replace_value = $replace_value;
	}

	function __toString() {
		return "REPLACE(".$this->field_name.",'".mysqli_real_escape_string(LDbConnectionManager::getLastConnectionUsed()->getHandle(),$this->search_value)."','".mysqli_real_escape_string(LDbConnectionManager::getLastConnectionUsed()->getHandle(),$this->replace_value)."')";
	}


}