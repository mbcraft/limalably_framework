<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlCreateViewStatement extends LMysqlAbstractQuery {
	
	private $view_name;
	private $replace_option = "";
	private $select_statement = null;

	function __construct($view_name) {
		$this->view_name = $view_name;
	}

	public function or_replace() {

		$this->replace_option = "OR REPLACE";
	
		return $this;
	}

	public function as($select) {
		if (!$select instanceof LMysqlSelectStatement) throw new \Exception("A select statement is needed to create a view!");

		$this->select_statement = $select;

		return $this;
	}

	public function __toString() {

		if (!$this->select_statement) throw new \Exception("Select statement has not been set in create view statement");

		return $this->build_query("CREATE",$this->replace_option,"VIEW",$this->view_name,"AS",$this->select_statement);
	}

}