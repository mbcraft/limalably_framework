<?php


class LMysqlForeignKeyChecksStatement extends LMysqlAbstractQuery {
	
	private $check_value;

	function __construct(bool $enable) {

		if ($enable) {
			$this->check_value = 1;
		} else {
			$this->check_value = 0;
		}
	}

	function __toString() {
		return "SET foreign_key_checks = ".$this->check_value;
	}


}