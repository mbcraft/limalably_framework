<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class ClonableObject {

	public $data;

}

class VarTest extends LTestCase {
	
	function testInheritanceWithStatic() {


		B_TestLib::$my_var = 3;

		$this->assertTrue(C_TestLib::$my_var==3,"La variabile statica è comune e viene copiata!");

		E_TestLib::$my_var = 5;

		$this->assertFalse(F_TestLib::$my_var==5,"La variabile statica è comune e viene copiata!");

		H_TestLib::$my_var = 7;

		$this->assertTrue(L_TestLib::$my_var==7,"La variabile statica è comune e viene copiata!");

	}

	function testClonation() {

		$ob = new ClonableObject();

		$ob->data = array("a" => 1,"b" => 2,"c" => "d");

		$cloned = clone $ob;

		$ob->data['a'] = 3;

		$this->assertEqual($cloned->data["a"],1,"Il dato si è copiato perfettamente anche come array!");

	}

}