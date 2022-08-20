<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlTruncateTableStatement extends LMysqlAbstractQuery {
	
	private $table_name;

	public function __construct($table_name) {
		if (!is_string($table_name)) throw new \Exception("Invalid string as table name in mysql truncate table statement.");
		$this->table_name = $table_name;
	}

	public function __toString() {
		return "TRUNCATE TABLE ".$this->table_name;
	}

}