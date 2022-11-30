<?php


class LHtmlStandardTagTable implements LITagRenderingTips {
	
	static $tag_definitions = [
		'br' => [
			'tag' => self::TAG_MODE_OPENCLOSE_NO_CONTENT,
			'indent' => self::INDENT_MODE_SKIP_ALL
		],
	];

	public static function hasTagDefinition($tag_name) {
		return isset(self::$tag_definitions[$tag_name]);
	}

	public static function setup($tag_name,$ltag) {

	}

	/*
	public static function autodefine_helper_functions() {
		//to be implemented in the future ...
	}
	*/
}