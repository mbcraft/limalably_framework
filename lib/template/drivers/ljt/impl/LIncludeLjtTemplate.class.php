<?php



class LIncludeLjtTemplate {
		
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
			$template_engine_class = LConfigReader::simple('/template/ljt/source_factory_class');

			$template_engine = new $template_engine_class('ljt');

			$template_root_folder = LConfigReader::simple('/template/ljt/root_folder');

			self::$file_template_source = $template_engine->createFileTemplateSource($template_root_folder);
		}

		$ljt_template_path = self::$file_template_source->searchTemplate($this->path);

		if (!$ljt_template_path) throw new \Exception("Template file do not exists at : ".$template_root_folder.$this->path." inside ".$this->getTreeDataPosition());

		$template_instance = self::$file_template_source->getTemplate($ljt_template_path);

		$template_instance->setNestedPosition($this->position."/INCLUDE");

		try {

    		return $template_instance->render($this->data);

    	} catch (\Exception $ex) {
    		throw new \Exception("Exception in rendering ljt template at position ".$this->position);
    	}
	}

	public function __toString() {
		return "".$this->render();
	}
}