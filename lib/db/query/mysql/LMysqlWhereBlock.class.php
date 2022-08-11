<?php


class LMysqlWhereBlock {
	

	private $element;

	function __construct($element) {

		ensure_instance_of("where block of mysql statement",$element,[LMysqlWhereCondition::class,LMysqlOrBlock::class,LMysqlAndBlock::class]);

		$this->element = $element;

	}

	function toSql() {
		return " WHERE ".$this->element;
	}

}