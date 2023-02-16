<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlTableName {
	

	private $result;

	function __construct($table_def,string $table_alias = null) {
		
		if ($table_def instanceof LMysqlSelectStatement) {
			if (!$table_alias) throw new \Exception("Table alias is mandatory when using select to define table name");
			$this->result = "( ".$table_def." )";
		} else {
			$this->result = $table_def;
		}
		if ($table_alias) {
			$this->result .= " ".$table_alias;
		}

	}

	function __toString() {
		return $this->result;
	}

}