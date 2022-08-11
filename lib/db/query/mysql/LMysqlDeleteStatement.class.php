<?php


class LMysqlDeleteStatement
{
	private $table;
	private $where_condition;

	function __construct($table,$where_condition) {
		ensure_instance_of($where_condition,[LMysqlWhereCondition::class]);
	
		$this->table = $table;
		$this->where_condition = $where_condition;
	}

	function __toString() {
		return "DELETE FROM ".$this->table." ".$this->where_condition.";";
	}
	
}