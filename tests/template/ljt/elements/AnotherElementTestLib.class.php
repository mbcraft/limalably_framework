<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class AnotherElementTestLib extends LJAbstractTemplatePart {
	
	const SIMPLE_FIELDS = ['AE-ONE','AE-TWO'];
	const MANDATORY_FIELDS = ['AE-TWO'];

	public function __toString() {
		
		$result = "<another_element AE-TWO='".$this('AE-TWO')."' ";

		if ($this->has('AE-ONE')) $result .= "AE-ONE='".$this('AE-ONE')."' ";

		$result .=">";
		$result .= "</another_element>";

		return $result;
	}

}