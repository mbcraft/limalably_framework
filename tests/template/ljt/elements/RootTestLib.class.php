<?php



class RootTestLib extends LJAbstractTemplatePart {
	
	const SIMPLE_FIELDS = ['root_one','root_two'];
	const TEMPLATE_FIELDS = ['t_root_one','t_root_two'];
	const TEMPLATE_ARRAY_FIELDS = ['ta_root_one','ta_root_two'];

	public function __toString() {

		$result = "<root_element ";
		if (isset($this->data['root_one'])) $result.="one='".$this->data['root_one']."' ";
		if (isset($this->data['root_one'])) $result.="two='".$this->data['root_two']."' ";
		$result .= ">";

		if (isset($this->data['t_root_one'])) $result .= $this->data['t_root_one'];
		if (isset($this->data['t_root_two'])) $result .= $this->data['t_root_two'];

		if (isset($this->data['ta_root_one'])) {
			$result .= "<list>";
			$result .= $this->data['ta_root_one'];
			$result .= "</list>";
		}

		if (isset($this->data['ta_root_two'])) {
			$result .= "<list>";
			$result .= $this->data['ta_root_two'];
			$result .= "</list>";
		}

		$result.= "</root_element>";

		return $result;
			
	}
}