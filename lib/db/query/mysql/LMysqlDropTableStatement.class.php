<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlDropTableStatement extends LMysqlAbstractQuery {
	

	private $table_name;
	private $temporary_modifier = "";
	private $if_exists_option = "";

	function __construct($table_name) {

		if (strpos($table_name,'%')!==false) {
			$this->table_name = "LIKE '".$table_name."'"; 
		}
		else {
			$this->table_name = $table_name;
		}

	}

	function temporary() {
		$this->temporary_modifier = "TEMPORARY";

		return $this;
	}

	function if_exists() {
		$this->if_exists_option = "IF EXISTS";

		return $this;
	}

	function __toString() {
		return $this->build_query('DROP',$this->temporary_modifier,'TABLE',$this->if_exists_option,$this->table_name);
	}

}