<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlPrivilegeDescription implements LIPrivilegeDescription {
	
	private $privilege;
	private $context;
	private $comment;

	function __construct($privilege,$context,$comment) {

		$this->privilege = $privilege;
		$this->context = $context;
		$this->comment = $comment;

	}

	public function getPrivilegeName() {
		return $this->privilege;
	}

	public function getContext() {
		return $this->context;
	}

	public function getComment() {
		return $this->comment;
	}


}