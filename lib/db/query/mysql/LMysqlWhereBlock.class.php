<?php


class LMysqlWhereBlock extends LMysqlAbstractConditionsBlock {
	
	public function __construct($element) {
		parent('where',$element);
	}
}