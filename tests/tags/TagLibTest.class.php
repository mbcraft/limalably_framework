<?php



class TagLibTest extends LTestCase {
	

	function setUp() {

		$pd = new LDir($_SERVER['FRAMEWORK_DIR'].'tests/tags/fake_project/');

		$_SERVER['PROJECT_DIR'] = $pd->getFullPath();
	}

	function testTagLib() {

		$tag1 = LTagLib::createTagFromLibrary('my_tag1');

		$tag2 = LTagLib::createTagFromLibrary('my_tag2');

		$tag12 = LTagLib::createTagFromLibrary('my_tag1');

		$tag22 = LTagLib::createTagFromLibrary('my_tag2');
	}

	function tearDown() {
		unset($_SERVER['PROJECT_DIR']);
	}

}