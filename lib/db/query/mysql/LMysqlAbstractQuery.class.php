<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

abstract class LMysqlAbstractQuery {


	protected function build_query(... $parts) {

		$final_part_list = [];
		foreach ($parts as $p) {
			if ($p == null || trim("".$p) == null) continue;
			$final_part_list [] = $p;
		}
		return implode(' ',$final_part_list);
	}

	function go($connection) {

		if (!$connection) throw new \Exception("Connection is not set!");

		if (!$connection->isOpen()) $connection->open();

		$connection_handle = $connection->getHandle();

		//echo "executing query : ".$this.";";

		$result = mysqli_query($connection_handle,$this.";");

		if (!$result) throw new \Exception("Mysql query failed : ".mysqli_error($connection_handle));
		
		if ($this instanceof LMysqlInsertStatement) {
			
			return mysqli_insert_id($connection_handle);
			
		}
		if ($this instanceof LMysqlSelectStatement) {
			$full_result = [];

			while ($row = mysqli_fetch_assoc($result)) $full_result[] = $row;
			return $full_result;
		}
	}

	function end() {
		return $this.";";
	}

	function iterator($connection) {

		if (!$connection) throw new \Exception("Connection is not set!");

		if (!$connection->isOpen()) $connection->open();

		$connection_handle = $connection->getHandle();

		$result = mysqli_query($connection_handle,$this->end(),MYSQLI_USE_RESULT);

		return new LMysqlResultIterator($result);
		
	}

}