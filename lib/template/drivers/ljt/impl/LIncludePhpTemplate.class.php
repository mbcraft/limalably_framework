<?php



class LIncludePhpTemplate {

	private $path;
	private $data;
	private $position;
	
	private static $template_engine = null;
	private static $file_template_source = null;

	public function __construct($path,$data,$position) {
		$this->path = $path;
		$this->data = $data;
		$this->position = $position;
	}

	public function render() {

		if (!self::$template_engine) {
			$template_engine_class = LConfigReader::simple('/template/php/source_factory_class');

			$template_engine = new $template_engine_class('php');

			$template_root_folder = LConfigReader::simple('/template/php/root_folder');

			self::$file_template_source = $template_engine->createFileTemplateSource($template_root_folder);
		}

		$php_template_path = self::$file_template_source->searchTemplate($this->path);

		if (!$php_template_path) throw new \Exception("Template file do not exists at : ".$template_root_folder.$this->path." inside ".$this->getTreeDataPosition());

		$template_instance = self::$file_template_source->getTemplate($php_template_path);

		try {

    		return $template_instance->render($this->data);

    	} catch (\Exception $ex) {
    		throw new \Exception("Exception in rendering php template at position ".$this->position);
    	}
	}

	public function __toString() {
		return "".$this->render();
	}

}