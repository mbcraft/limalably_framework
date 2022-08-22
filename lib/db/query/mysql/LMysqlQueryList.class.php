<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlQueryList {
	

	private $query_list;

	function __construct($query_list_or_file) {

		if ($query_list_or_file instanceof LFile) {
			$this->query_list = $query_list_or_file->getContent();
			return;
		} 
		if (is_string($query_list_or_file))
		{
			$this->query_list = $query_list_or_file;
			return;
		}

		throw new \Exception("Unrecognized parameter for mysql query list. Actually only string or LFile instances are allowed");

	}

	function go_no_results($connection) {
		if (!$connection) throw new \Exception("Connection is not set!");

		if (!$connection->isOpen()) $connection->open();

		$connection_handle = $connection->getHandle();

		mysqli_multi_query($connection_handle,$this->query_list);

		do {
			$result = mysqli_store_result($connection_handle);
			//ok, continue
		} while (mysqli_next_result($connection_handle));
	}

	function go($connection) {

		if (!$connection) throw new \Exception("Connection is not set!");

		if (!$connection->isOpen()) $connection->open();

		$connection_handle = $connection->getHandle();

		mysqli_multi_query($connection_handle,$this->query_list);

		$results = [];

		do {

			while ($query_result = mysqli_store_result($connection_handle)) {
				$res = [];
				while ($row = mysqli_fetch_assoc($query_result)) {
					$res[] = $row;
				}
				$results[] = $res;

			}

		} while(mysqli_next_result($connection_handle));	
		
		return $results;
	}

}