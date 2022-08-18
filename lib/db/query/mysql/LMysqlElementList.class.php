<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlElementList {
	
	private $elements;

	public function __construct(... $elements) {

		$this->elements = $elements;
	}

	public function toRawStringList() {
		ensure_all_strings("elements in mysql element list",$this->elements);

		if (empty($this->elements)) return "";

		return "(".implode(',',$this->elements).")";
	}

	public function toRawStringListWithoutParenthesis() {
		ensure_all_strings_or_null("elements in mysql element list",$this->elements);

		return implode(',',$this->elements);
	}

	public function toEscapedStringList() {
		ensure_all_numbers_or_strings_or_null("elements in mysql element list",$this->elements);

		$converted_elements = [];

		foreach ($this->elements as $elem) {
			if ($elem === null) $converted_elements[] = 'NULL';
			if (is_string($elem)) {
				$converted_elements[] = "'".mysqli_real_escape_string(LDbConnectionManager::getLastConnectionUsed()->getHandle(),$elem)."'";
			} else {
				$converted_elements[] = $elem;
			}
		}

		return "(".implode(',',$converted_elements).")";
	}

	public function __toString() {
		return $this->toEscapedStringList();
	}

}