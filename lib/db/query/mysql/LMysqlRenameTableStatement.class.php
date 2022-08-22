<?php


class LMysqlRenameTableStatement extends LMysqlAbstractQuery {

	private $old_table_name;

	private $new_table_name;
	

	function __construct($old_table_name,$new_table_name) {

		$this->old_table_name = $old_table_name;

		$this->new_table_name = $new_table_name;

	}

	function __toString() {
		return "RENAME TABLE ".$this->old_table_name." TO ".$this->new_table_name; 
	}

}