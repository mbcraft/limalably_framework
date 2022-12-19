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

		$result.= "</root_element>";

		return $result;
			
	}
}