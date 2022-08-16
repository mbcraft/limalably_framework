<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlResultIterator implements LIResultIterator {
	
	private $result;
	private $last_assoc_row;

	function __construct($result) {
		$this->result = $result;
		$this->last_assoc_row = true;
	}


	function hasMore() {
		return $this->last_assoc_row;
	}

	function nextRow() {
		if (!$this->result) throw new \Exception("Unable to fetch more rows from result!");

		$this->last_assoc_row = mysqli_fetch_assoc($this->result);

		return $this->last_assoc_row;
	}

	function stop() {
		if ($this->hasMore()) {
			mysqli_free_result($this->result);
			$this->last_assoc_row = null;
			$this->result = null;
		}
	}

}

