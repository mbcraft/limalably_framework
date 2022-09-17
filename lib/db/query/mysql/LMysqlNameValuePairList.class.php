<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlNameValuePairList {
	
	private $name_value_pair_list;

	public function __construct(array $name_value_pair_list) {

		$keys = array_keys($name_value_pair_list);

		ensure_all_strings("name value pair block of mysql update statement",$keys);

		$values = array_values($name_value_pair_list);

		ensure_all_numbers_or_strings_or_null("name value pair block of mysql update statement",$values);

		$this->name_value_pair_list = $name_value_pair_list;

	}

	public function __toString() {

		$elements = array();

		foreach ($this->name_value_pair_list as $k => $v) {
			$el = $k." = ";
			
			$el .= new LMysqlValueRenderer($v);

			$elements[] = $el;
		}

		return implode(',',$elements);

	}
}