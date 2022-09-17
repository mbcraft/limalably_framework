<?php



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