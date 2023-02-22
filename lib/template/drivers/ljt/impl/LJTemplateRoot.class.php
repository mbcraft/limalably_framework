<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LJTemplateRoot extends LJAbstractTemplatePart implements LITreeDataPosition {
	
	private $my_template_data = null;
	private $starting_pos;

	function __construct($data) {
		$this->my_template_data = $data;
	}

	public function setupStartingPosition($starting_pos) {
		$this->starting_pos = $starting_pos;
	}

	public function parseFully() {

		$this->tree_data_position = '/'.$this->starting_pos;

		$this->parseAsTemplateField('LAYOUT',$this->my_template_data);

	}

	public function getTreeDataPosition() {
		return $this->tree_data_position;
	}

	public function dumpTreeDataPositions() {

		echo "ROOT : ".$this->tree_data_position."\n";

		$this->LAYOUT->dumpTreeDataPositions();
	}


	public function overrideParameters(array $params) {
		$this->my_template_data = array_merge($this->my_template_data,$params);
	}

	public function render() {
		return $this->LAYOUT;
	}

	public function __toString() {

		return "".$this->render();

	}

}