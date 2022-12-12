<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlLastAffectedRows {
	

	function go($connection) {
		if (!$connection) throw new \Exception("Connection is not set!");

		if (!$connection->isOpen()) $connection->open();

		$connection_handle = $connection->getHandle();

		return mysqli_affected_rows($connection_handle);
	}

	function iterate($connection) {
		throw new \Exception("Iterate is not supported for this operation. Use go().");
	}

}