<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlElementListList {
	
	private $lists = [];

	public function add($element_list_or_array) {
		if (is_array($element_list_or_array)) $el = new LMysqlElementList(... $element_list_or_array); 
		else $el = $element_list_or_array;
		if (!$el instanceof LMysqlElementList) throw new \Exception("Only mysql element list are allowed inside element list list");
		
		$this->lists[] = $el;

		return $this;
	}

	private function checkNotEmptyArray($data) {
		if (is_array($data) && empty($data)) throw new \Exception("Found empty array in element list list - in mysql statement");
	}

	private function arrayContainsStringsOrNumbers($arr) {
		foreach ($arr as $arr_element) {
			if (is_string($arr_element) || is_numeric($arr_element)) return true;
			else return false;
		}
	}

	private function arrayContainsElementLists($arr) {
		if ($arr instanceof LMysqlElementList) return true;
		foreach ($arr as $arr_element) {
			if ($arr_element instanceof LMysqlElementList) return true;
		}
		return false;
	}

	private function parseAllArrayAsElementLists($arr) {
		$result = array();

		if ($arr instanceof LMysqlElementList) {
			$result[] = $arr;
			return $result;
		}

		foreach ($arr as $arr_element) {
			if ($arr_element instanceof LMysqlElementList) $result[] = $arr_element;
			else $result[] = new LMysqlElementList($arr_element);
		}
		return $result;
	}

	private function init($prepared_lists) {
		ensure_all_instances_of("data part of mysql insert",$prepared_lists,[LMysqlElementList::class]);
		
		$this->lists = $prepared_lists;
	}

	public function __construct(... $lists) {
		
		if (empty($lists)) return;

		$prepared_lists = array();

		//1
		if ($this->arrayContainsStringsOrNumbers($lists)) {
			$prepared_lists = array(new LMysqlElementList($lists));

			return $this->init($prepared_lists);
		}

		if ($this->arrayContainsElementLists($lists)) {
			$prepared_lists = $this->parseAllArrayAsElementLists($lists);

			return $this->init($prepared_lists);
		}

		if (count($lists)==1 && is_array($lists[0])) {
			//2
			$this->checkNotEmptyArray($lists[0]);

			if ($this->arrayContainsElementLists($lists[0])) {
				$prepared_lists = $this->parseAllArrayAsElementLists($lists[0]);

				return $this->init($prepared_lists);
			}

			if ($this->arrayContainsStringsOrNumbers($lists[0])) {
				$prepared_lists = array(new LMysqlElementList($lists[0]));

				return $this->init($prepared_lists);
			}

			$prepared_lists = $this->parseAllArrayAsElementLists($lists[0]);

			return $this->init($prepared_lists);
		}

		if (count($lists)>1) {
			foreach ($lists as $el) {
				$this->checkNotEmptyArray($el);
			
				if ($el instanceof LMysqlElementList)
					$prepared_lists[] = $el;
				else
					$prepared_lists[] = new LMysqlElementList($el);
			}

			return $this->init($prepared_lists);
		}		
		
		
	}

	public function __toString() {

		if (empty($this->lists)) throw new \Exception("Mysql element list list can't be empty");

		$sql_pieces = [];
		foreach ($this->lists as $l) {
			$sql_pieces []= $l->toEscapedStringList();
		}

		return implode(',',$sql_pieces);

	}
}