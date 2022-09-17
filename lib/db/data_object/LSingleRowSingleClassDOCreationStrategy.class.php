<?php


class LSingleRowSingleClassDOCreationStrategy {
	
	private $do_class_name;

	function __construct(string $do_class_name) {
		$this->do_class_name = $do_class_name;
	}

	function createInstance($row) {

		$instance = new $this->do_class_name();

		$instance->__fillWithData($row);

		return $instance;

	}
}