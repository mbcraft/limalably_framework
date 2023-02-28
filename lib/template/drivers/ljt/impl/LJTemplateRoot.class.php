<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJTemplateRoot extends LJAbstractTemplatePart {
	
	private $my_template_data = null;
	private $starting_pos;
	private $parsed = false;

	function __construct($data) {
		$this->my_template_data = $data;
	}

	public function setupStartingPosition($starting_pos) {
		$this->starting_pos = $starting_pos;
	}

	private function parseFully() {

		if ($this->parsed) return;

		$this->tree_data_position = '/'.$this->starting_pos;

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

	public function __toString() {

		return "".$this->render();

	}

}