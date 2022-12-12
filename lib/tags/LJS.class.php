<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

function require_js(string $name,string $path,$version='unknown') {
	LJS::require($name,$path,$version);
}

class LJS {

	use LAssetResourceManagerTrait;
	
	public static function renderTag($spec) {

		$tag = new LTag('script');

		$tag->setTagMode(LTag::TAG_MODE_OPEN_EMPTY_CLOSE);
		$tag->setIndentMode(LTag::TAG_INDENT_SKIP_ALL);
		$tag->setTagName('script');

		$tag->type("text/javascript");
		$tag->href($spec['path']);

		return $tag;
	}
}