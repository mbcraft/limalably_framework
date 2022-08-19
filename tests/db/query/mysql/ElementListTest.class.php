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

	function testEmptyElementList() {

		db('framework_unit_tests');

		try {
			$el = el();
			$this->fail("Non è stata lanciata un'eccezione per una lista di elementi vuota!");
		} catch (\Exception $ex) {
			//ok
		}

	}

	function testElementListWithEmptyArray() {

		db('framework_unit_tests');

		try {
			$el = el([]);
			$this->fail("Non è stata lanciata un'eccezione per una lista di elementi con dentro un array vuoto!");
		} catch (\Exception $ex) {
			//ok
		}

	}



}