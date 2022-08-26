<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlCaseColumn {
	
	private $when_list = [];

	private $else_trailer = "";

	private $column_name;

	function __construct($column_name) {

		if (!is_string($column_name)) throw new \Exception("Column name is not a valid string in mysql case function");

		$this->column_name = c($column_name);
	}

	function when($condition,$case_value) {
		$value = new LMysqlValueRenderer($case_value);

		$this->when_list[] = "WHEN ".$condition." THEN ".$value;

		return $this;
	}

	function default($case_value) {
		$value = new LMysqlValueRenderer($case_value);

		$this->else_trailer = "ELSE ".$value;

		return $this;
	}

	protected function build_query(... $parts) {

		$final_part_list = [];
		foreach ($parts as $p) {
			if ($p == null || trim("".$p) == null) continue;
			$final_part_list [] = $p;
		}
		return implode(' ',$final_part_list);
	}

	function __toString() {

		if (empty($this->when_list)) throw new \Exception("At least one when is needed in mysql case function");

	
		return $this->build_query("(","CASE ",implode("\n",$this->when_list),$this->else_trailer,"END",") ",$this->column_name);
	}


}