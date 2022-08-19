<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class ElementListTest extends LTestCase {
	
	function testElementListWithString() {

		db('framework_unit_tests');

		$el = el('prova');

		$this->assertEqual($el->toRawStringList(),"(prova)","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toRawStringListWithoutParenthesis(),"prova","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toEscapedStringList(),"('prova')","La stringa della lista di elementi non corrisponde a quello atteso!");
	}


	function testElementListWithMultipleParameters() {

		db('framework_unit_tests');

		$el = el('elemento1','elemento2','elemento3');			

		$this->assertEqual($el->toRawStringList(),"(elemento1,elemento2,elemento3)","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toRawStringListWithoutParenthesis(),"elemento1,elemento2,elemento3","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toEscapedStringList(),"('elemento1','elemento2','elemento3')","La stringa della lista di elementi non corrisponde a quello atteso!");
	}

	function testElementListWithArray() {

		db('framework_unit_tests');

		$el = el(['elemento1','elemento2','elemento3']);

		$this->assertEqual($el->toRawStringList(),"(elemento1,elemento2,elemento3)","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toRawStringListWithoutParenthesis(),"elemento1,elemento2,elemento3","La stringa della lista di elementi non corrisponde a quello atteso!");

		$this->assertEqual($el->toEscapedStringList(),"('elemento1','elemento2','elemento3')","La stringa della lista di elementi non corrisponde a quello atteso!");

		

	}



}