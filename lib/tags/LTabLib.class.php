<?php

function tag(string $original_tag_name) {
	return LTagLib::createTagFromLibrary($original_tag_name);
}

function tagref($child_name) {
	return new LTagReference($child_name);
}

class LTagLib {
	
	const TAGLIB_DIR = "taglib/";

	static $library = array();	

	const SPEC_TAG = "tag";
	const SPEC_MODE = "mode";
	const SPEC_INDENT = "indent";
	const SPEC_REQUIRED_ATTRIBUTES = "required_attributes";
	const SPEC_REQUIRED_STRING_IN_ATTRIBUTES = "required_string_in_attributes";
	const SPEC_REQUIRED_CHILDREN = "required_children";
	const SPEC_DEFAULT_ATTRIBUTES = "default_attributes";

	const SPEC_MODE_VALUES = ['OPEN_CONTENT_CLOSE','OPEN_EMPTY_CLOSE','OPEN_ONLY','OPENCLOSE_NO_CONTENT'];
	const SPEC_INDENT_VALUES = ['NORMAL','SKIP_ALL'];

	static function findTagSpecFile(string $original_tag_name) {

		$starting_dir = new LDir(self::TAGLIB_DIR);

		if (!$starting_dir->exists()) return null;

		return self::recursiveFindTagSpecFile($original_tag_name,$starting_dir);

	}

	static function recursiveFindTagSpecFile(string $original_tag_name,$folder) {

		$possible_spec_file = $folder->newFile($original_tag_name.".json");

		if ($possible_spec_file->exists()) return $possible_spec_file;

		$folders = $folder->listFolders();

		foreach ($folders as $fold) {
			$result = self::recursiveFindTagSpecFile($original_tag_name,$fold);
			if ($result) return $result;
		}

		return null;

	}

	static function loadTagSpecFromFileAndCreateOriginal($spec_file) {

		$original_tag_name = $spec_file->getName();

		$content = $spec_file->getContent();

		$spec_data = json_decode($content,true);

		self::verifyTagSpecification($original_tag_name,$spec_data);

		return self::createWithSpec($original_tag_name,$spec_data);

	}

	private static function checkAllStrings($original_tag_name,$name,$data) {
		
		if (!is_array($data)) throw new \Exception("Values are not specified as array in ".$name." of ".$original_tag_name);

		foreach ($data as $dt) {
			if (!is_string($dt)) throw new \Exception("Not all values are string from ".$name." in ".$original_tag_name);
		}
	}

	private static function verifyTagSpecification(string $original_tag_name,$spec_data) {
		if (!is_array($spec_data)) throw new \Exception("Error in specification for tag ".$original_tag_name);

		if (!isset($spec_data[self::SPEC_TAG]) && !is_string($spec_data[self::SPEC_TAG])) throw new \Exception("Tag specification is missing from ".$original_tag_name);
		unset($spec_data[self::SPEC_TAG]);

		if (!isset($spec_data[self::SPEC_MODE]) && !is_string($spec_data[self::SPEC_MODE])) throw new \Exception("Mode specification is missing from ".$original_tag_name);
		if (!LStringUtils::contains($spec_data[self::SPEC_MODE],self::SPEC_MODE_VALUES)) throw new \Exception("Mode value is not correct in ".$original_tag_name.". Only ".implode(',',self::SPEC_MODE_VALUES)." are admitted!");
		unset($spec_data[self::SPEC_MODE]);

		if (!isset($spec_data[self::SPEC_INDENT]) && !is_string($spec_data[self::SPEC_INDENT])) throw new \Exception("Mode specification is missing from ".$original_tag_name);
		if (!LStringUtils::contains($spec_data[self::SPEC_INDENT],self::SPEC_INDENT_VALUES)) throw new \Exception("Indent value is not correct in ".$original_tag_name.". Only ".implode(',',self::SPEC_INDENT_VALUES)." are admitted!");
		unset($spec_data[self::SPEC_INDENT]);

		if (isset($spec_data[self::SPEC_REQUIRED_ATTRIBUTES])) {
			if (!is_array($spec_data[self::SPEC_REQUIRED_ATTRIBUTES])) throw new \Exception("required_attributes must be an array in ".$original_tag_name);

			self::checkAllStrings($original_tag_name,self::SPEC_REQUIRED_ATTRIBUTES,$spec_data[self::SPEC_REQUIRED_ATTRIBUTES]);
		
			unset($spec_data[self::SPEC_REQUIRED_ATTRIBUTES]);
		} 

		if (isset($spec_data[self::SPEC_REQUIRED_STRING_IN_ATTRIBUTES])) {
			foreach ($spec_data[self::SPEC_REQUIRED_STRING_IN_ATTRIBUTES] as $attr_name => $list) {
				if (!is_string($attr_name)) throw new \Exception("Attribute name is not a string in required_string_in_attributes");

				if (!is_array($list)) throw new \Exception("Attribute string list specification for required_string_in_attributes is not an array in ".$original_tag_name."!");

				foreach ($list as $element) {
					self::checkAllStrings($original_tag_name,self::SPEC_REQUIRED_STRING_IN_ATTRIBUTES,$element);
				}

			}

			unset($spec_data[self::SPEC_REQUIRED_STRING_IN_ATTRIBUTES]);
		}

		if (isset($spec_data[self::SPEC_REQUIRED_CHILDREN])) {
			self::checkAllStrings($original_tag_name,self::SPEC_REQUIRED_CHILDREN,$spec_data[self::SPEC_REQUIRED_CHILDREN]);

			unset($spec_data[self::SPEC_REQUIRED_CHILDREN]);
		}

		if (isset($spec_data[self::SPEC_DEFAULT_ATTRIBUTES])) {

			foreach ($spec_data[self::SPEC_DEFAULT_ATTRIBUTES] as $attr_name => $data) {
				if (is_string($data)) continue;

				self::checkAllStrings($original_tag_name,$attr_name.' in default_attributes',$data);
			}

			unset($spec_data[self::SPEC_DEFAULT_ATTRIBUTES]);
		}

		if (!empty($spec_data)) throw new \Exception("Unknown specs in spec data for ".$original_tag_name);
	}

	private static function createWithSpec($original_tag_name,$spec) {

		$tag = new LTag($original_tag_name);

		$tag->setTagName($spec[self::SPEC_TAG]);

		switch ($spec[self::SPEC_MODE]) {
			case 'OPEN_CONTENT_CLOSE' : $my_mode = LITagRenderingTips::TAG_MODE_OPEN_CONTENT_CLOSE; break;
			case 'OPEN_EMPTY_CLOSE' : $my_mode = LITagRenderingTips::TAG_MODE_OPEN_EMPTY_CLOSE; break;
			case 'OPEN_ONLY' : $my_mode = LITagRenderingTips::TAG_MODE_OPEN_ONLY; break;
			case 'OPENCLOSE_NO_CONTENT' : $my_mode = LITagRenderingTips::TAG_MODE_OPENCLOSE_NO_CONTENT; break;

			default: throw new \Exception("Should never go here");
		}

		$tag->setTagMode($my_mode);

		switch ($spec[self::SPEC_INDENT])
		{
			case 'NORMAL' : $my_indent = LITagRenderingTips::TAG_INDENT_NORMAL; break;
			case 'SKIP_ALL' : $my_indent = LITagRenderingTips::TAG_INDENT_SKIP_ALL; break;

			default: throw new \Exception("Should never go here");
		}

		$tag->setIndentMode($my_indent);

		if (isset($spec[self::SPEC_REQUIRED_ATTRIBUTES])) {
			$required_attributes = $spec[self::SPEC_REQUIRED_ATTRIBUTES];

			foreach ($required_attributes as $ra) {
				$tag->addRequiredAttribute($ra);
			}
		}

		if (isset($spec[self::SPEC_REQUIRED_STRING_IN_ATTRIBUTES])) {
			$required_string_in_attribute = $spec[self::SPEC_REQUIRED_STRING_IN_ATTRIBUTES];

			foreach ($required_string_in_attribute as $attr_name => $list) {
				foreach ($list as $string_list) {
					$tag->addRequiredStringInAttribute($attr_name,$string_list);
				}
			}
		}

		if (isset($spec[self::SPEC_REQUIRED_CHILDREN])) {
			$required_children = $spec[self::SPEC_REQUIRED_CHILDREN];

			foreach ($required_children as $rc) {
				$tag->addRequiredChild($rc);
			}
		}

		if (isset($spec[self::SPEC_DEFAULT_ATTRIBUTES])) {
			$default_attributes = $spec[self::SPEC_DEFAULT_ATTRIBUTES];

			foreach ($default_attributes as $attr_name => $data) {
				if (is_string($data)) $tag->setAttribute($attr_name,$data);
				else {
					foreach ($data as $string) {
						$tag->setAttribute($attr_name,$string);
					}
				}
			}
		}

		return $tag;
	}

	static function createTagFromLibrary($original_tag_name) {

		if (!isset(self::$library[$original_tag_name])) 
 		{
			$spec_file = self::findTagSpecFile($original_tag_name);

			if (!$spec_file) throw new \Exception("Unable to find definition of tag ".$original_tag_name);

			$original = self::loadTagSpecFromFileAndCreateOriginal($spec_file);

			self::$library[$original_tag_name] = $original;
		} else {

 			$original = self::$library[$original_tag_name];
 		}
 		
 		return $original->makeClone();

		
	}

}