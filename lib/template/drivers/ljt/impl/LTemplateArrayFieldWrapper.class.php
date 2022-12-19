<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTemplateArrayFieldWrapper {
	
	private $my_data;

	function __construct($data) {
		$this->my_data = $data;
	}

	public function __toString() {
		$result = "";


		foreach ($this->my_data as $element) {
			$result .= $element;
		}

		return $result;
	}


}