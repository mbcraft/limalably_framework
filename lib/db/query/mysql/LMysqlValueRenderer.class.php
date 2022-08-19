<?php


class LMysqlValueRenderer {
	
	private $value;

	function __construct($value) {
		$this->value = $value;
	}

	function __toString() {

		if ($this->value === null) return 'NULL';
		if ($this->value instanceof LMysqlValuePlaceholder) return "".$this->value;
		if ($this->value instanceof LMysqlReplaceValue) return "".$this->value;
		if (is_numeric($this->value)) return "".$this->value;
		if (is_string($this->value)) return "'".mysqli_real_escape_string(LDbConnectionManager::getLastConnectionUsed()->getHandle(),$this->value)."'";
		if ($this->value === false) return '0';
		if ($this->value === true) return '1';

		throw new \Exception("Unrecognized mysql value : ".$value);

	}

}