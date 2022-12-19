<?php


class BasicTemplateRenderingTest extends LTestCase {
	

	function testExampleT1() {

		$tf = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/template/ljt/examples/t1.json');

		$content = $tf->getContent();

		$this->assertTrue(strlen($content)>0,"Il template caricato risulta vuoto!");

		$ti = new LJTemplate($content);

		try {
			$ti->render([]);
			
		} catch (\Exception $ex) {
			$this->fail("Si è verificata un eccezione in fase di rendering del template : ".$ex->getMessage());
		}


	}

	function testExampleT2() {

		$tf = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/template/ljt/examples/t2_error.json');

		$content = $tf->getContent();

		$this->assertTrue(strlen($content)>0,"Il template caricato risulta vuoto!");

		$ti = new LJTemplate($content);

		try {
			$ti->render([]);
			$this->fail("Il rendering del template sbagliato non è fallito!");
		} catch (\Exception $ex) {

		}

	}

	
	function testExampleT3() {

		$tf = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/template/ljt/examples/t3.json');

		$content = $tf->getContent();

		$this->assertTrue(strlen($content)>0,"Il template caricato risulta vuoto!");

		$ti = new LJTemplate($content);

		try {
			$result = $ti->render([]);
			
			$this->assertEqual($result,"<root_element one='a_value' two='another_value' ></root_element>","Il risultato del template non corrisponde a quello atteso!");

		} catch (\Exception $ex) {
			$this->fail("Si è verificata un eccezione in fase di rendering del template : ".$ex->getMessage());
		}

	}


	function testExampleT4() {

		$tf = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/template/ljt/examples/t4_json_error.json');

		$content = $tf->getContent();

		$this->assertTrue(strlen($content)>0,"Il template caricato risulta vuoto!");

		

		try {

			$ti = new LJTemplate($content);

			$result = $ti->render([]);
			
			$this->fail("Si è verificata un eccezione in fase di rendering del template : ".$ex->getMessage());

		} catch (\Exception $ex) {
			//all is ok
		}

	}
	
}