<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlElementListList {
	
	private $lists;

	public function __construct(... $lists) {
		
		ensure_all_instances_of("data part of mysql insert",$lists,[LMysqlElementList::class]);
		
		$this->lists = $lists;
	}

	public function __toString() {


		$sql_pieces = [];
		foreach ($this->lists as $l) {
			$sql_pieces []= $l->toEscapedStringList();
		}

		return implode(',',$sql_pieces);

	}
}