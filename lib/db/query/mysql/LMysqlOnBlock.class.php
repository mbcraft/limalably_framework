<?php


class LMysqlOnBlock extends LMysqlAbstractConditionsBlock {
	

	public function __construct($element) {
		parent('on',$element);
	}

}