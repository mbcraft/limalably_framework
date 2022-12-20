<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

abstract class LJAbstractTemplatePart {

	const SIMPLE_FIELDS = [];
	const TEMPLATE_FIELDS = [];
	const TEMPLATE_ARRAY_FIELDS = [];

	const MANDATORY_FIELDS = [];

	private $data = [];

	private $tree_data_position = null;

	public function __construct() {
		$this->checkFieldsDefinitions();
	}

	private function checkFieldsDefinitions() {
		$all_names_count = count(static::SIMPLE_FIELDS)+count(static::TEMPLATE_FIELDS)+count(static::TEMPLATE_ARRAY_FIELDS);

		$all_fields = array_merge(static::SIMPLE_FIELDS,static::TEMPLATE_FIELDS,static::TEMPLATE_ARRAY_FIELDS);

		if (count($all_fields)!=$all_names_count) throw new \Exception("It is not possible to use the same field name for different field types!");

		foreach (static::MANDATORY_FIELDS as $f) {
			if (!in_array($f,$all_fields)) throw new \Exception("The mandatory field ".$f." is not defined in any field type for this template part!");
		}
	}

	private function checkMandatoryFields($array_data) {
		foreach (static::MANDATORY_FIELDS as $k) {
			if (!isset($array_data[$k])) throw new \Exception("The mandatory field ".$k." is not defined at the position ".$this->tree_data_position);
		}
	}

	public function setTreeDataPosition($position) {
		$this->tree_data_position = $position;
	}

	public function getTreeDataPosition() {
		return $this->tree_data_position;
	}

	public static function createTemplatePart($template_class_name,$position,$data) {
		try {
			$template_instance = new $template_class_name();

		} catch (\Exception $ex) {
			throw new \Exception("Error during creation of template part '".$template_class_name."': ".$ex->getMessage());
		}

		$template_instance->setTreeDataPosition($position);

		try {
			$template_instance->parse($data);
		} catch (\Exception $ex) {
			throw new \Exception("Error during template part parsing at position ".$position." : ".$ex->getMessage());
		}

		return $template_instance;
	}

	private static function getTemplateClassNameFromDef($template_def) {

		$keys = array_keys($template_def);

		if (count($keys)>1) throw new \Exception("Only one template is allowed in this array! (".$this->tree_data_position.")");
		$template_class_name = $keys[0];

		if (is_numeric($template_class_name)) throw new \Exception("It is necessary to use a string as a template part name!"); 

		return $template_class_name;
	}

	public function parseAsSimpleField($key,$value) {

		$this->data[$key] = $value;
	}

	public function parseAsTemplateField($key,$template_def) {

		$template_class_name = self::getTemplateClassNameFromDef($template_def);

		$template_data = $template_def[$template_class_name];

		$template_instance = self::createTemplatePart($template_class_name,$this->tree_data_position.'/'.$key.'/'.$template_class_name,$template_data);

		$this->data[$key] = $template_instance;
	}

	public function parseAsTemplateArrayField($key,$template_array_def) {
		
		$keys = array_keys($template_array_def);

		foreach ($keys as $k) {
			if (is_string($k)) throw new \Exception("String keys not allowed inside template part arrays!");
		}

		$field_result = [];

		foreach ($keys as $k) {
			$template_def = $template_array_def[$k];

			$template_class_name = self::getTemplateClassNameFromDef($template_def);

			$template_data = $template_def[$template_class_name];

			$template_instance = self::createTemplatePart($template_class_name,$this->tree_data_position.'/'.$key.'/'.$template_class_name,$template_data);

			$field_result[] = $template_instance;
		}

		$this->data[$key] = new LTemplateArrayFieldWrapper($field_result);
	}

	public function parse($array_data) {

		if (!$this->tree_data_position) throw new \Exception("Tree data position is not set!");

		$this->checkMandatoryFields($array_data);

		foreach ($array_data as $key => $value) {

			if (in_array($key,static::SIMPLE_FIELDS)) {

				$value = $array_data[$key];

				$this->parseAsSimpleField($key,$value);
				continue;
			}

			if (in_array($key,static::TEMPLATE_FIELDS)) {

				$template_def = $array_data[$key];

				$this->parseAsTemplateField($key,$template_def);
				continue;
			}

			if (in_array($key,static::TEMPLATE_ARRAY_FIELDS)) {

				$template_array_def = $array_data[$key];

				$this->parseAsTemplateArrayField($key,$template_array_def);
				continue;
			}

			throw new \Exception("Invalid field ".$key." found inside ".$this->tree_data_position.". Check your template syntax.");
		}

	}

	public function has($field_name) {
		if (!is_string($field_name)) throw new \Exception("field_name is not a valid string in element ".$this->tree_data_position);

		return isset($this->data[$field_name]);
	}

	public function __invoke(...$params) {
		if (count($params)!=1) throw new \Exception("Exactly one parameter as 'field name' allowed.");

		$field_name = $params[0];

		if (!$this->has($field_name)) throw new \Exception("Readed field ".$field_name." does not exist on ".$this->tree_data_position);

		return $this->data[$field_name];
	}

	public function __toString() {
		return "".$this->render();
	}

	public abstract function render();


	
}