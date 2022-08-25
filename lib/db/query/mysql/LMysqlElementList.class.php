<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlElementList {
	
	private $elements;

	private function checkNoArrayElementsOrElInside($arr) {
		foreach ($arr as $arr_element) {
			if (is_array($arr_element)) throw new \Exception("Invalid element list : contains an array");
			if ($arr_element instanceof self) throw new \Exception("Invalid element list : contains another element list");
		}
	}

	public function getElements() {
		return $this->elements;
	}

	public function __construct(... $elements) {

		if (count($elements)==0) return;

		if (count($elements)==1 && is_array($elements[0])) {

			if (empty($elements[0])) throw new \Exception("Invalid element list : Empty array found - in mysql statement.");

			$this->checkNoArrayElementsOrElInside($elements[0]);

			$keys = array_keys($elements[0]);

			ensure_all_numbers("mysql element list",$keys);

			$this->elements = $elements[0];
		}
		else {

			$this->checkNoArrayElementsOrElInside($elements);

			$keys = array_keys($elements);

			ensure_all_numbers("mysql element list",$keys);

			$this->elements = $elements;
		}
	}

	public function add($element) {
		$this->checkNoArrayElementsOrElInside([$element]);

		$this->elements[] = $element;

		return $this;
	}

	private function checkNotEmpty() {
		if (count($this->elements)==0) throw new \Exception("Invalid element list : zero elements found - in mysql statement.");
	}

	public function toRawStringList() {

		$this->checkNotEmpty();

		ensure_all_strings("elements in mysql element list",$this->elements);

		if (empty($this->elements)) return "";

		return "(".implode(',',$this->elements).")";
	}

	public function toRawStringListWithoutParenthesis() {

		$this->checkNotEmpty();

		return implode(',',$this->elements);
	}

	public function toEscapedStringList() {

		$this->checkNotEmpty();

		$converted_elements = [];

		foreach ($this->elements as $elem) {
			$converted_elements[] = new LMysqlValueRenderer($elem);
		}

		return "(".implode(',',$converted_elements).")";
	}

	public function __toString() {
		return $this->toEscapedStringList();
	}

}