<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJTemplateRoot extends LAbstractTemplatePart {
	
	private $my_tree_data = null;

	function __construct($data) {
		$this->my_tree_data = $data;
	}

	public function parse() {

		$this->parseAsTemplateField('root',$this->my_tree_data);

	}

	public function __toString() {

		return "".$this->data['root'];

	}

}