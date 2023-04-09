<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJTemplateRoot extends LJAbstractTemplate {

	private $my_template_data = null;
	private $parsed = false;

	function __construct($data) {
		$this->my_template_data = $data;
	}

	private function parseFully() {

		if ($this->parsed) return;

		$this->tree_data_position = '/';

		$this->parseAsTemplateField('LAYOUT',$this->my_template_data);

		$this->parsed = true;
	}

	public function isParsed() {
		return $this->parsed;
	}

	public function render() {

		$this->parseFully();

		return $this->LAYOUT;
	}

}