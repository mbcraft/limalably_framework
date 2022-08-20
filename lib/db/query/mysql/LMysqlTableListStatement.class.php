<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlTableListStatement extends LMysqlAbstractQuery {
	

	public function __toString() {
		return "SHOW TABLES";
	}

	public static function parse_table_list($result) {
		
	}

}