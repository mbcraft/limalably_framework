<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LCSS {

	use LAssetResourceManagerTrait;
	
	public static function renderTag($spec) {
		$tag = new LTag('link');

		$tag->setTagMode(LTag::TAG_MODE_OPENCLOSE_NO_CONTENT);
		$tag->setIndentMode(LTag::TAG_INDENT_SKIP_ALL);
		$tag->setTagName('link');

		$tag->rel("stylesheet");
		$tag->href($spec['path']);

		return $tag;
	}
	
}