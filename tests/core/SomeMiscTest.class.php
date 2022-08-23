<?php


class SomeMiscTest extends LTestCase {
	

	function testStrpos() {

		
		$string = "my_table_name";
		$search = 'me';

		$this->assertEqual(strpos($string,$search),11,"Il risultato della strpos non è corretto!");

		$string = "me%q";

		$search = '%';

		$this->assertEqual(strpos($string,$search),2,"Il risultato della search non è corretto!");
		
		$string = "my_table_name";

		$this->assertTrue(strpos($string,$search)===false,"Il risultato della search non è corretto!");
	}

	function testArrayValueExists() {

		$data = ['a' => 1,'b' => 2,'c' => 3,'d' => 'k'];

		$this->assertTrue(array_value_exists($data,1),"Il valore cercato non esiste!");
		$this->assertTrue(array_value_exists($data,2),"Il valore cercato non esiste!");
		$this->assertTrue(array_value_exists($data,3),"Il valore cercato non esiste!");
		$this->assertTrue(array_value_exists($data,'k'),"Il valore cercato non esiste!");
		$this->assertFalse(array_value_exists($data,'z'),"Il valore cercato non esiste!");
	}
}