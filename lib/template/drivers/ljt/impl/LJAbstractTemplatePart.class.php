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

	const EXTERNAL_TEMPLATE = null;

	private $data = [];

	private $global_data = [];

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

	public function hasField($field_name) {
		return array_key_exists($field_name,$this->data);
	}

	public function getField($field_name) {
		return $this->data[$field_name];
	}

	public function setupGlobalData($global_data) {

		$this->global_data = $global_data;

		foreach (static::TEMPLATE_FIELDS as $field_name) {

			if ($this->hasField($field_name)) {

				$field = $this->getField($field_name);
				
				$field->setupGlobalData($global_data);
			}

		}

		foreach (static::TEMPLATE_ARRAY_FIELDS as $field_name) {

			if ($this->hasField($field_name)) {

				$array_field = $this->getField($field_name);

				foreach ($array_field as $element) {
					$element->setupGlobalData($global_data);
				}

			}

		}


	}

	private function renderExternalTemplate($parameters) {

		if (!self::externalTemplateFileExists()) return "EXTERNAL TEMPLATE FILE NOT FOUND : ".self::getExternalTemplateFile()->getFullPath();

		$path = static::EXTERNAL_TEMPLATE;

		if (!$path) return null;

		$template_rendering = new LTemplateRendering();
		if (self::hasPhpExternalTemplate()) $template_rendering->setupTemplateSource('php');
		if (self::hasTwigExternalTemplate()) $template_rendering->setupTemplateSource('twig');

		$result = $template_rendering->render($path,$parameters);

		return $result;
	}

	private static function externalTemplateFileExists() {
		return self::getExternalTemplateFile()->exists();
	}

	private static function getExternalTemplateFile() {
		$path = static::EXTERNAL_TEMPLATE;

		if (!$path) return null;

		$template_rendering = new LTemplateRendering();
		if (self::hasPhpExternalTemplate()) $template_rendering->setupTemplateSource('php');
		if (self::hasTwigExternalTemplate()) $template_rendering->setupTemplateSource('twig');

		$pre_final_path = $template_rendering->searchTemplate($path);

		if ($template_rendering->hasRootFolder()) $final_path = $template_rendering->getRootFolder() . $pre_final_path;

		return new LFile($final_path);
		
	}

	private static function hasExternalTemplate() {
		return static::EXTERNAL_TEMPLATE != null;
	}

	private static function hasPhpExternalTemplate() {
		if (static::EXTERNAL_TEMPLATE) {
			return LStringUtils::endsWith(static::EXTERNAL_TEMPLATE,'php');
		} else return false;
	}

	private static function hasTwigExternalTemplate() {
		if (static::EXTERNAL_TEMPLATE) {
			return LStringUtils::endsWith(static::EXTERNAL_TEMPLATE,'twig');
		} else return false;
	}

	private static function getTemplateDataFromTemplateDef($template_def) {

		if (isset($template_def['t'])) unset($template_def['t']);

		return $template_def;

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

		if (!isset($template_def['t'])) throw new \Exception("No template found in template definition : ".var_export($template_def,true));

		$template_class_name = $template_def['t'];

		if (is_numeric($template_class_name)) throw new \Exception("It is necessary to use a string as a template part name!"); 

		return $template_class_name;
	}

	public function parseAsSimpleField($key,$value) {

		$this->data[$key] = $value;
	}

	public function parseAsTemplateField($key,$template_def) {

		$template_class_name = self::getTemplateClassNameFromDef($template_def);

		$template_data = self::getTemplateDataFromTemplateDef($template_def);

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

			$template_data = self::getTemplateDataFromTemplateDef($template_def);

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

	public function __get($field_name) {
		return $this->data[$field_name];
	}
	
	public function __invoke(...$params) {
		if (count($params)!=1) throw new \Exception("Exactly one parameter as 'field name' allowed.");

		$field_name = $params[0];

		if (!$this->has($field_name)) throw new \Exception("Readed field ".$field_name." does not exist on ".$this->tree_data_position);

		return $this->data[$field_name];
	}
	
	public function t() {
		return $this->__toString();
	}

	public function __toString() {
		return "".$this->render();
	}

	public function render() {

		$parameters = array_merge($this->global_data,$this->data);

		if ($this->hasExternalTemplate()) {
			return $this->renderExternalTemplate($parameters);
		} else {
			

			return $this->customRenderImpl($parameters);
		}
	}

	protected function customRenderImpl($parameters) {
		return "EMPTY CUSTOM RENDER IMPL for class ".static::class;
	}


	
}