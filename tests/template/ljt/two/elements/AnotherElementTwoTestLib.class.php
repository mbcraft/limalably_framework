<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class AnotherElementTwoTestLib extends LJAbstractTemplatePart {
	
	const SIMPLE_FIELDS = ['AE-ONE','AE-TWO'];
	const MANDATORY_FIELDS = ['AE-TWO'];

	public function __toString() {
		
		$tag = new LTag('another_element_two');
		$tag->setTagName('another_element_two');
		$tag->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$tag->setIndentMode(LTag::TAG_INDENT_SKIP_ALL);

		$tag->AE__TWO($this('AE-TWO'));

		if ($this->has('AE-ONE')) $tag->AE__ONE($this('AE-ONE'));

		return "".$tag;
	}

}