<?php


class LMysqlElementList {
	
	private $elements;

	function __construct(... $elements) {
		$this->elements = $elements;
	}

	function toRawStringList() {
		ensure_all_strings($this->elements)

		return "(".implode(',',$this->elements).")";
	}

	function toEscapedStringList() {
		ensure_all_numbers_or_strings($this->elements);

		$converted_elements = [];

		foreach ($this->elements as $elem) {
			if (is_string($elem)) {
				$converted_elements[] = "'".$elem."'";
			} else {
				$converted_elements[] = $elem;
			}
		}

		return "(".implode(',',$converted_elements).")";
	}

	function __toString() {
		return $this->toEscapedStringList();
	}

}