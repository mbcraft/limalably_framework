<?php

class LMysqlNameValuePairList {
	
	private $name_value_pair_list;

	function __construct(... $name_value_pair_list) {

		$keys = array_keys($name_value_pair_list);

		ensure_all_strings("name value pair block of mysql update statement",$keys);

		$values = array_values($name_value_pair_list);

		ensure_all_numbers_or_strings("name value pair block of mysql update statement",$values);

		$this->name_value_pair_list = $name_value_pair_list;

	}

	public __toString() {

		$elements = array();

		foreach ($this->name_value_pair_list as $k => $v) {
			$el = $k." = ";
			if (is_string($v)) {
				$el .= "'".$v."'";
			} else {
				$el .= $v;
			}
		}

		return implode(',',$elements);

	}
}