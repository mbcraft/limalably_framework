<?php


class ElementOneTestLib extends LJAbstractTemplatePart {
	
	const SIMPLE_FIELDS = ['e1-one','e1-two'];
	const TEMPLATE_FIELDS = ['te1-one','te1-two'];
	const MANDATORY_FIELDS = ['e1-one'];

	public function __toString() {
		return "";
	}

}