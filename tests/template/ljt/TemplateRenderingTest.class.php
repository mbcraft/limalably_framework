<?php


class TemplateRenderingTest extends LTestCase {
	

	private $MY_JSON_1 = <<<EOD
{
	"t" : "OneTestLib",
	"text" : "Hello world!",
	"content" : {
		"t" : "OneAnotherTestLib",
		"text" : "Hello again!"
	}
} 
EOD;

	function testSimpleTemplateRender() {

		$template = new LJTemplate("/my/test/location/somewhere",$this->MY_JSON_1);

		echo $template->render([]);

	}



}