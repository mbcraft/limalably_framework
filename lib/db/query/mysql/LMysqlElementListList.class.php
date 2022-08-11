<?php


class LMysqlElementListList {
	
	private $lists;

	function __construct(... $lists) {
		
		ensure_all_instances_of("data part of mysql insert",$lists,[LMysqlElementList::class]);
		
		$this->lists = $lists;
	}

	function __toString() {


		$sql_pieces = [];
		foreach ($this->lists as $l) {
			$sql_pieces []= $l->toEscapedStringList();
		}

		return implode(',',$sql_pieces);

	}
}