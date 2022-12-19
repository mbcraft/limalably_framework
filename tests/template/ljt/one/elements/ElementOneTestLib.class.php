<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class ElementOneTestLib extends LJAbstractTemplatePart {
	
	const SIMPLE_FIELDS = ['e1-one','e1-two'];
	const TEMPLATE_FIELDS = ['te1-one','te1-two'];
	const MANDATORY_FIELDS = ['e1-one'];

	public function render() {}

	public function __toString() {
		$result = "<element_one e1-one='".$this('e1-one')."' ";

		if ($this->has('e1-two')) $result.= "e1-two='".$this('e1-two')."' ";

		$result.=">";

		if ($this->has('te1-one')) $result.=$this('te1-one');
		if ($this->has('te1-two')) $result.=$this('te1-two');

		$result .= "</element_one>";

		return $result;

	}

}