<?php




class VarTest extends LTestCase {
	
	function testInheritanceWithStatic() {


		B_TestLib::$my_var = 3;

		$this->assertTrue(C_TestLib::$my_var==3,"La variabile statica è comune e viene copiata!");

		E_TestLib::$my_var = 5;

		$this->assertFalse(F_TestLib::$my_var==5,"La variabile statica è comune e viene copiata!");

		H_TestLib::$my_var = 7;

		$this->assertTrue(L_TestLib::$my_var==7,"La variabile statica è comune e viene copiata!");

	}

}