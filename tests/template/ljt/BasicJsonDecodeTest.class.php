<?php



class BasicJsonDecodeTest extends LTestCase {
	

	function testTemplateDecode() {

		$template_file = new LFile('tests/template/ljt/source/hello_world.ljt');

		$this->assertTrue($template_file->exists(),"Il file del template non esiste!");

		$content = $template_file->getContent();

		$data = json_decode($content,true);

		$this->assertTrue(is_array($data),"I dati decodificati non sono un array php!!");


	}

}