<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class RootTwoTestLib extends LJAbstractTemplatePart {
	
	const SIMPLE_FIELDS = ['root_one','root_two'];
	const TEMPLATE_FIELDS = ['t_root_one','t_root_two'];
	const TEMPLATE_ARRAY_FIELDS = ['ta_root_one','ta_root_two'];

	public function __toString() {

		$tag = new LTag('root_two');
		$tag->setTagName('root_two');
		$tag->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$tag->setIndentMode(LTag::TAG_INDENT_SKIP_ALL);

		
		if ($this->has('root_one')) $tag->one($this('root_one'));
		if ($this->has('root_one')) $tag->two($this('root_two'));
		
		if ($this->has('t_root_one')) $tag []= $this('t_root_one');
		if ($this->has('t_root_two')) $tag []= $this('t_root_two');

		if ($this->has('ta_root_one')) {
			$list_tag = new LTag('list');
			$list_tag->setTagName('list');
			$list_tag->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
			$list_tag->setIndentMode(LTag::TAG_INDENT_SKIP_ALL);

			$list_tag [] = $this('ta_root_one'); 

			$tag [] = $list_tag;
		}

		if ($this->has('ta_root_two')) {
			$list_tag = new LTag('list');
			$list_tag->setTagName('list');
			$list_tag->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
			$list_tag->setIndentMode(LTag::TAG_INDENT_SKIP_ALL);

			$list_tag [] = $this('ta_root_two'); 

			$tag [] = $list_tag;
		}

		return "".$tag;
			
	}
}