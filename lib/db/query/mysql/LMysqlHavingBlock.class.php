<?php


class LMysqlHavingBlock extends LMysqlAbstractConditionsBlock {
	
	public function __construct($element) {
		parent('having',$element);
	}


}