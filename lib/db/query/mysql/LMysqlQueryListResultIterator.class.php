<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/


class LMysqlQueryListResultIterator implements LIResultIterator {
	

	function __construct($connection_handle) {
		$this->connection_handle = $connection_handle;
	}

	private $has_next = true;

	public function hasNext() {
		return $this->has_next;
	}

	public function next() {
		
		$result = null;

		while ($query_result = mysqli_store_result($this->connection_handle)) {
			$result = null;
			while ($row = mysqli_fetch_assoc($query_result)) {
				if ($result === null) $result = array();

				$result[] = $row;
			}

		}
		
		$this->has_next = mysqli_next_result($this->connection_handle);

		return $result;
	}

	public function stop() {
		if ($this->hasNext()) {
			mysqli_store_result($this->connection_handle);

			$this->has_next = mysqli_next_result($this->connection_handle);
		}
	}



}