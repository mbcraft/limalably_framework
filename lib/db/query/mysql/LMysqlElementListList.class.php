<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlElementListList {
	
	private $lists;

	public function __construct(... $lists) {
		
		if (is_array($lists[0])) {
			$prepared_lists = [];
			foreach ($lists as $l) {
				$prepared_lists[] = new LMysqlElementList($l);
			}
		} 
		else {
			$prepared_lists = $lists;
		}
		ensure_all_instances_of("data part of mysql insert",$prepared_lists,[LMysqlElementList::class]);
		
		$this->lists = $prepared_lists;
	}

	public function __toString() {


		$sql_pieces = [];
		foreach ($this->lists as $l) {
			$sql_pieces []= $l->toEscapedStringList();
		}

		return implode(',',$sql_pieces);

	}
}