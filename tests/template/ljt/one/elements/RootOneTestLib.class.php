<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class RootOneTestLib extends LJAbstractTemplatePart {
	
	const SIMPLE_FIELDS = ['root_one','root_two'];
	const TEMPLATE_FIELDS = ['t_root_one','t_root_two'];
	const TEMPLATE_ARRAY_FIELDS = ['ta_root_one','ta_root_two'];

	public function __toString() {

		$result = "<root_one ";
		if ($this->has('root_one')) $result.="one='".$this('root_one')."' ";
		if ($this->has('root_one')) $result.="two='".$this('root_two')."' ";
		$result .= ">";

		if ($this->has('t_root_one')) $result .= $this('t_root_one');
		if ($this->has('t_root_two')) $result .= $this('t_root_two');

		if ($this->has('ta_root_one')) {
			$result .= "<list>";
			$result .= $this('ta_root_one');
			$result .= "</list>";
		}

		if ($this->has('ta_root_two')) {
			$result .= "<list>";
			$result .= $this('ta_root_two');
			$result .= "</list>";
		}

		$result.= "</root_one>";

		return $result;
			
	}
}