<?php


abstract class LMysqlAbstractQuery {
	
	private $connection_handle;

	function setupConnectionHandle($connection_handle) {
		$this->connection_handle = $connection_handle;
	}

	function go() {
		$result = mysqli_execute($this->connection_handle,$this.";");
		
		if ($this instanceof LMysqlInsertStatement) return mysqli_insert_id($this->connection_handle);
		if ($this instanceof LMysqlSelectStatement) return mysqli_
	}

	function end() {
		return $this.";";
	}

	function iterator() {
		$result = mysqli_execute($this->connection_handle,$this->end());
		
	}

}