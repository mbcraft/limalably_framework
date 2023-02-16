<?php


class WriteTextTestLib extends LJAbstractTemplatePart {
	
	const SIMPLE_FIELDS = ['text'];

	const MANDATORY_FIELDS = ['text'];

	function render() {
		return "".$this->getField('text');
	}


}