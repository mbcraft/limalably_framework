<?php



class JSTest extends LTestCase {
	

	function testBasicRendering() {

		LJS::overrideStandardWwwPath($_SERVER['FRAMEWORK_DIR'].'tests/tags/fake_project/wwwroot');

		LJS::require('jquery','/assets/js/jquery.1.3.0.js','1.3.0');
		LJS::require('mylib','/assets/js/mylib.js');

		LJS::require('jquery','/assets/js/jquery.1.5.0.js','1.5.0');
		LJS::require('another_lib','/assets/js/another_lib.js');

		$tag_list = LJS::getTagList();
		$this->assertEqual(count($tag_list),3,"Il numero dei tag in uscita non corrisponde!");

		$this->assertEqual(LJS::getCurrentLibraryVersion('jquery'),'1.5.0',"La versione corrente della libreria non corrisponde!");

	}

	function testArrayBehaviour() {


		$data = [];

		$data['hello'] = 12;
		$data['b'] = 1;
		$data['a'] = 3;
		$data['prova'] = 5;

		$keys = array_keys($data);

		$this->assertEqual($keys[0],'hello',"La chiave non corrisponde!");
		$this->assertEqual($keys[1],'b',"La chiave non corrisponde!");
		$this->assertEqual($keys[2],'a',"La chiave non corrisponde!");
		$this->assertEqual($keys[3],'prova',"La chiave non corrisponde!");
	}

}