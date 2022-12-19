<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class ElementTwoTestLib extends LJAbstractTemplatePart {
	
	const SIMPLE_FIELDS = ['e1-one','e1-two'];
	const TEMPLATE_FIELDS = ['te1-one','te1-two'];
	const MANDATORY_FIELDS = ['e1-one'];

	public function __toString() {

		$tag = new LTag('element_two');
		$tag->setTagName('element_two');
		$tag->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$tag->setIndentMode(LTag::TAG_INDENT_SKIP_ALL);

		$tag->e1__one($this('e1-one'));

		if ($this->has('e1-two')) $tag->e1__two($this('e1-two'));

		if ($this->has('te1-one')) $tag[]=$this('te1-one');
		if ($this->has('te1-two')) $tag[]=$this('te1-two');

		return "".$tag;

	}

}