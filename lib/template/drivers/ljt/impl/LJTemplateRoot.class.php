<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJTemplateRoot extends LJAbstractTemplatePart {
	
	private $my_template_data = null;

	function __construct($data) {
		$this->my_template_data = $data;
	}

	public function parseFully() {

		$this->tree_data_position = '/';

		$this->parseAsTemplateField('LAYOUT',$this->my_template_data);

	}

	public function render() {
		return $this->LAYOUT;
	}

	public function __toString() {

		return "".$this->render();

	}

}