<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTemplateArrayFieldWrapper {
	
	private $collection;

	function __construct($data) {
		$this->collection = $data;
	}

	public function __toString() {
		$result = "";


		foreach ($this->collection as $element) {
			$result .= $element;
		}

		return $result;
	}

	public function isFirst($elem) {
		$keys = array_keys($this->collection);
		$key0 = $keys[0];

		if ($this->collection[$key0]==$elem) return true;
		else return false;
	}

	public function isLast($elem) {
		$keys = array_keys($this->collection);
		$key_last = end($keys);

		if ($this->collection[$key_last]==$elem) return true;
		else return false;	
	}

}