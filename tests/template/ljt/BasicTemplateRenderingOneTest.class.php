<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class BasicTemplateRenderingOneTest extends LTestCase {
	
	
	function testExampleT1() {

		$tf = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/template/ljt/one/examples/t1.json');

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

		$tf = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/template/ljt/one/examples/t2_error.json');

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

		$tf = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/template/ljt/one/examples/t3.json');

		$content = $tf->getContent();

		$this->assertTrue(strlen($content)>0,"Il template caricato risulta vuoto!");

		$ti = new LJTemplate($content);

		try {
			$result = $ti->render([]);
			
			$this->assertEqual($result,"<root_one one='a_value' two='another_value' ></root_one>","Il risultato del template non corrisponde a quello atteso!");

		} catch (\Exception $ex) {
			$this->fail("Si è verificata un eccezione in fase di rendering del template : ".$ex->getMessage());
		}

	}
	

	function testExampleT4() {

		$tf = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/template/ljt/one/examples/t4_json_error.json');

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


	function testExampleT1Again() {

		$tf = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/template/ljt/one/examples/t1.json');

		$content = $tf->getContent();

		$this->assertTrue(strlen($content)>0,"Il template caricato risulta vuoto!");

		$ti = new LJTemplate($content);

		try {
			$result = $ti->render([]);

			$this->assertEqual($result,"<root_one one='a_value' two='another_value' ><element_one e1-one='12' e1-two='ab' ><another_element_one AE-TWO='xyz' AE-ONE='3' ></another_element_one></element_one><list><another_element_one AE-TWO='2' AE-ONE='1' ></another_element_one><another_element_one AE-TWO='abc' ></another_element_one><another_element_one AE-TWO='abcd' AE-ONE='123' ></another_element_one></list></root_one>","Il risultato non corrisponde a quello atteso!");
			
		} catch (\Exception $ex) {
			$this->fail("Si è verificata un eccezione in fase di rendering del template : ".$ex->getMessage());
		}


	}
	
}