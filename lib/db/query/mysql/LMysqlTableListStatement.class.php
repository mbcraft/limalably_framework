<?php

class LMysqlTableListStatement {
	

	public function __toString() {
		return "SHOW TABLES";
	}

	public static function parse_table_list($result) {
		
	}

}