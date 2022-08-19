<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlElementListList {
	
	private $lists;

	public function __construct(... $lists) {
		
		if (empty($lists)) throw new \Exception("Empty element list list is not allowed in mysql insert statement");
		
		if (count($lists)==1) {
			if (is_string($lists[0])) {
				$prepared_lists = array(new LMysqlElementList($lists));
			}

			
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