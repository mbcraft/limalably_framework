<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlShowPrivilegesStatement extends LMysqlAbstractQuery {
	

	function __construct() {}

	function __toString() {
		return "SHOW PRIVILEGES";
	}

	function go($connection) {
		$result = parent::go($connection);

		$privileges_list = [];

		foreach ($result as $row) {
			$privileges_list[$row['Privilege']] = new LMysqlPrivilegeDescription($row['Privilege'],$row['Context'],$row['Comment']);
		}

		return $privileges_list;
	}

	function iterator($connection) {

		throw new \Exception("iterator function is not supported for this statement.Use 'go' and get the full result");
	
	}



}