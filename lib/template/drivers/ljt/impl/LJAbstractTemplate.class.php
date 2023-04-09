<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

abstract class LJAbstractTemplate {

	const SIMPLE_FIELDS = [];
	const TEMPLATE_FIELDS = [];
	const TEMPLATE_ARRAY_FIELDS = [];

	const MANDATORY_FIELDS = [];

	const AVAILABLE_METHODS = ['render'];

	private $local = [];

	protected $global = [];

	private $tree_data_position = null;

	private $render_method = 'render';

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

	private function setRenderMethod($method_name) {

		if (!in_array($method_name,static::AVAILABLE_METHODS)) throw new \Exception("The method for rendering named '".$method_name."' is not available.");

		$this->render_method = $method_name;

	}

	public function hasField($field_name) {
		return array_key_exists($field_name,$this->local);
	}

	public function getField($field_name) {
		return $this->local[$field_name];
	}

	public function setupGlobalData($global) {

		$this->global = $global;

		foreach (static::TEMPLATE_FIELDS as $field_name) {

			if ($this->hasField($field_name)) {

				$field = $this->getField($field_name);
				
				$field->setupGlobalData($global);
			}

		}

		foreach (static::TEMPLATE_ARRAY_FIELDS as $field_name) {

			if ($this->hasField($field_name)) {

				$array_field = $this->getField($field_name);

				foreach ($array_field as $element) {
					$element->setupGlobalData($global);
				}

			}

		}

	}

	private static function getTemplateDataFromTemplateDef($template_def) {

		if (isset($template_def['t'])) unset($template_def['t']);

		return $template_def;

	}

	public static function createTemplateInstance($template_name_spec,$position,$data) {

		$template_spec_parts = explode(':',$template_name_spec);

		$template_class_name = $template_spec_parts[0];

		$render_method = 'render';

		if (count($template_spec_parts)==2) $render_method = $template_spec_parts[1];

		try {

			if (!class_exists($template_class_name)) throw new \Exception("Template class ".$template_class_name." not found!");

			$template_instance = new $template_class_name();

		} catch (\Exception $ex) {
			throw new \Exception("Error during creation of template part '".$template_name_spec."': ".$ex->getMessage());
		}

		$template_instance->setRenderMethod($render_method);

		$template_instance->setTreeDataPosition($position.$template_name_spec);

		try {
			$template_instance->parse($data);
		} catch (\Exception $ex) {
			throw new \Exception("Error during template part parsing at position ".$position." : ".$ex->getMessage());
		}

		return $template_instance;
	}

	private static function getTemplateNameSpecFromDef($template_def) {

		if (!isset($template_def['t'])) throw new \Exception("No template found in template definition : ".var_export($template_def,true));

		$template_name_spec = $template_def['t'];

		if (is_numeric($template_name_spec) || is_bool($template_name_spec)) throw new \Exception("It is necessary to use a string as a template part name!"); 

		return $template_name_spec;
	}

	public function parseAsSimpleField($key,$value) {

		$this->local[$key] = $value;
	}

	public function parseAsTemplateField($key,$template_def) {

		$template_name_spec = self::getTemplateNameSpecFromDef($template_def);

		$template_data = self::getTemplateDataFromTemplateDef($template_def);

		$template_instance = self::createTemplateInstance($template_name_spec,$this->tree_data_position.'/'.$key.'/',$template_data);

		$this->local[$key] = $template_instance;
	}

	public function parseAsTemplateArrayField($key,$template_array_def) {
		
		$keys = array_keys($template_array_def);

		foreach ($keys as $k) {
			if (is_string($k)) throw new \Exception("String keys not allowed inside template part arrays!");
		}

		$field_result = [];

		foreach ($keys as $k) {
			$template_def = $template_array_def[$k];

			$template_name_spec = self::getTemplateNameSpecFromDef($template_def);

			$template_data = self::getTemplateDataFromTemplateDef($template_def);

			$template_instance = self::createTemplateInstance($template_name_spec,$this->tree_data_position.'/'.$key.'/'.$template_class_name,$template_data);

			$field_result[] = $template_instance;
		}

		$this->local[$key] = new LTemplateArrayFieldWrapper($field_result);
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

			//Commentata la riga in quanto tutti i dati passati facevano saltare per aria il rendering
			throw new \Exception("Invalid field ".$key." found inside ".$this->tree_data_position.". Check your template syntax.");
		}

	}

	public function has($field_name) {
		if (!is_string($field_name)) throw new \Exception("field_name is not a valid string in element ".$this->tree_data_position);

		return isset($this->local[$field_name]);
	}

	public function __get($field_name) {
		if (isset($this->local[$field_name])) return $this->local[$field_name];
		
		throw new \Exception("Unable to find field ".$field_name." inside template at ".$this->tree_data_position);
			
	}
	
	public function __toString() {
		return $this->internalRender();
	}

	public function internalRender() {

		ob_start();

		$result = $content = null;

		try {

			$render_method = $this->render_method;

			$result = $this->{$render_method}();
		
			$content = ob_get_contents();

			ob_end_clean();
		} catch (\Exception $ex) {
		
			ob_end_clean();

			$result = $ex->getMessage();
		} 

		if ($result && $content) throw new \Exception("Only one between returned string and output can be used!");

		if ($result!=null) return $result;
		if ($content!=null) return $content;

	}

	protected function render() {

		return "EMPTY RENDER IMPL for class ".static::class;
	}
	
}