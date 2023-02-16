<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlStandardResultIterator implements LIResultIterator {
	
	private $result;
	private $last_assoc_row;

	function __construct($result) {
		$this->result = $result;
		$this->last_assoc_row = true;
	}


	function hasNext() {
		$this->last_assoc_row = mysqli_fetch_assoc($this->result);

		return $this->last_assoc_row!=null;
	}

	function next() {
		return $this->last_assoc_row;
	}

	function stop() {
		if ($this->hasNext()) {
			mysqli_free_result($this->result);
			$this->last_assoc_row = null;
			$this->result = null;
		}
	}

}

