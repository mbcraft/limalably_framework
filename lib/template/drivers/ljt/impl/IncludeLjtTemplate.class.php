<?php



class IncludeLjtTemplate extends LJAbstractTemplatePart {
		
	const SIMPLE_FIELDS = ['template'];

	const MANDATORY_FIELDS = ['template'];

	private static $template_engine = null;
	private static $file_template_source = null;

	public function render() {

		if (!self::$template_engine) {
			$template_engine_class = LConfigReader::simple('/template/ljt/source_factory_class');

			$template_engine = new $template_engine_class('ljt');

			$template_root_folder = LConfigReader::simple('/template/ljt/root_folder');

			self::$file_template_source = $template_engine->createFileTemplateSource($template_root_folder);
		}

		$template_path = $this->getField('template');

		$ljt_template_path = self::$file_template_source->searchTemplate($template_path);

		if (!$ljt_template_path) throw new \Exception("Template file do not exists at : ".$template_root_folder.$template_path." inside ".$this->getTreeDataPosition());

		$template_instance = self::$file_template_source->getTemplate($ljt_template_path);

    	return $template_instance->render([]);
	}
}