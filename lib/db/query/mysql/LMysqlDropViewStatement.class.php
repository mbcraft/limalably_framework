<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LMysqlDropViewStatement extends LMysqlAbstractQuery {
	
	private $view_name;
	private $if_exists_option = "";

	function __construct($view_name) {
		$this->view_name = $view_name;
	}

	public function if_exists() {

		$this->if_exists_option = "IF EXISTS";

	}

	public function __toString() {

		return $this->build_query("DROP VIEW",$this->if_exists_option,$this->view_name);

	}

}