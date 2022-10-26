<?php



class MoreFileAndDirTest extends LTestCase {

	function testDirWithPath() {

		$d = new LDir('something/');

		$this->assertEqual($d->getPath(),'something/',"Il path non corrisponde!!");
		$this->assertEqual($d->getFullPath(),$_SERVER['FRAMEWORK_DIR'].'something/',"Il full path non coincide!");

		$d2 = new LDir($_SERVER['FRAMEWORK_DIR'].'something/');

		$this->assertEqual($d2->getPath(),'something/',"Il path non corrisponde!!");
		$this->assertEqual($d2->getFullPath(),$_SERVER['FRAMEWORK_DIR'].'something/',"Il full path non coincide!");

	}

	function testFileWithPath() {

		$f = new LFile('my_file.txt');

		$this->assertEqual($f->getPath(),'my_file.txt',"Il path del file non corrisponde!");
		$this->assertEqual($f->getFullPath(),$_SERVER['FRAMEWORK_DIR'].'my_file.txt',"Il full path del file non corrisponde!");

		$f2 = new LFile($_SERVER['FRAMEWORK_DIR'].'my_file.txt');

		$this->assertEqual($f2->getPath(),'my_file.txt',"Il path del file non corrisponde!");
		$this->assertEqual($f2->getFullPath(),$_SERVER['FRAMEWORK_DIR'].'my_file.txt',"Il full path del file non corrisponde!");
	}
}