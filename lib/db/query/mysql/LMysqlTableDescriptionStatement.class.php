<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LMysqlTableDescriptionStatement extends LMysqlAbstractQuery {
	
	private $table_name;

	public function __construct($table_name) {

		if (!is_string($table_name)) throw new \Exception("The table description table name is not a valid string.");

		$this->table_name = $table_name;
	}

	public function __toString() {
		return "DESC ".$this->table_name;
	}

	public static function parse_table_description($result) {
		$column_description_list = [];

		foreach ($result as $row) {
			$column_description_list[] = new LMysqlColumnDescription($row['Field'],$row['Type'],$row['Null'],$row['Key'],$row['Default'],$row['Extra']);
		}

		return $column_description_list;
	}

}