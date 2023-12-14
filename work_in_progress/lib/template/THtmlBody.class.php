<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class THtmlBody extends LJAbstractTemplatePart {
	
	const TEMPLATE_ARRAY_FIELDS = ['content'];

	function render() {

		$tl = new LTagList();

		foreach ($this->content as $element) $tl[] = $element;

		return $tl;

	}

}
