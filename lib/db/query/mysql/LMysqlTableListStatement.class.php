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

	public function go($connection) {
		if (!$connection) throw new \Exception("Connection is not set!");

		if (!$connection->isOpen()) $connection->open();

		$connection_handle = $connection->getHandle();

		$result = mysqli_query($connection_handle,$this.";");

		if (!$result) throw new \Exception("Mysql query failed : ".mysqli_error($connection_handle));
		
		$full_result = [];

		while ($row = mysqli_fetch_assoc($result)) $full_result[] = $row;
	
		$final_result = [];

		foreach ($full_result as $row) {
			$final_result[] = array_values($row)[0];
		}

		return $final_result;
		
	}

}