<?php



class IncludePhpTemplate extends LJAbstractTemplatePart {
	
	const SIMPLE_FIELDS = ['vars','template'];

	const MANDATORY_FIELDS = ['vars','template'];
	
	private static $template_engine = null;
	private static $file_template_source = null;

	public function render() {

		if (!self::$template_engine) {
			$template_engine_class = LConfigReader::simple('/template/php/source_factory_class');

			$template_engine = new $template_engine_class('php');

			$template_root_folder = LConfigReader::simple('/template/php/root_folder');

			self::$file_template_source = $template_engine->createFileTemplateSource($template_root_folder);
		}

		$template_path = $this->getField('template');

		$php_template_path = self::$file_template_source->searchTemplate($template_path);

		if (!$php_template_path) throw new \Exception("Template file do not exists at : ".$template_root_folder.$template_path." inside ".$this->getTreeDataPosition());

		$template_instance = self::$file_template_source->getTemplate($php_template_path);

    	return $template_instance->render($this->getField('vars'));
	}

}