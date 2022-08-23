<?php

class LMysqlDropTableStatement extends LMysqlAbstractQuery {
	

	private $table_name;
	private $if_exists_option = "";

	function __construct($table_name) {

		if (strpos($table_name,'%')!==false) {
			$this->table_name = "LIKE '".$table_name."'"; 
		}
		else {
			$this->table_name = $table_name;
		}

	}

	function if_exists() {
		$this->if_exists_option = "IF EXISTS";

		return $this;
	}

	function __toString() {
		return $this->build_query('DROP','TABLE',$this->if_exists_option,$this->table_name);
	}

}