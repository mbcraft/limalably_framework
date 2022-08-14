<?php


abstract class LMysqlAbstractQuery {
	
	private $connection_handle;

	function setupConnectionHandle($connection_handle) {
		$this->connection_handle = $connection_handle;
	}

	function go() {
		$result = mysqli_query($this->connection_handle,$this.";");

		if (!$result) throw new \Exception("Mysql query failed : ".mysqli_error($this->connection_handle));
		
		if ($this instanceof LMysqlInsertStatement) return mysqli_insert_id($this->connection_handle);
		if ($this instanceof LMysqlSelectStatement) {
			$full_result = [];

			while ($row = mysqli_fetch_assoc($result)) $full_result[] = $row;
			return $full_result;
		}
	}

	function end() {
		return $this.";";
	}

	function iterator() {
		$result = mysqli_query($this->connection_handle,$this->end(),MYSQLI_USE_RESULT);

		return new LMysqlResultIterator($result);
		
	}

}