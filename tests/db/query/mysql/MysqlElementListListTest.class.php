<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MysqlElementListListTest extends LTestCase {
	

	function testElementListListWithAdd() {
		db('framework_unit_tests');
		
		$ell4 = ell();	// ell con dentro 2 el - array di array -> array con 2 el

		$ell4->add(['a','b','c']);
		$ell4->add(['d','e']);

		$this->assertEqual($ell4,"('a','b','c'),('d','e')","La lista di liste non ritorna i dati attesi!");
		
	}

	function testElementListList() {
		
		db('framework_unit_tests');
		
		try {
			$ell0 = ell();	// ell vuota - non ammessa
			$this->fail("La lista di liste non lancia un'eccezione quando è vuota!");
		} catch (\Exception $ex) {
			//ok
		}

		$ell1 = ell('a','b','c'); // array di stringhe -> array con 1 el

		try {
			$ell2 = ell([]); // ell con array vuoto - non ammessa
			$this->fail("La lista di liste non lancia un'eccezione quando contiene un array vuoto!");
		} catch (\Exception $ex) {
			//ok
		}

		$ell3 = ell(['a','b','c']);	// ell con dentro 1 el - array di array -> array con 1 el

		
		$ell4 = ell(['a','b','c'],['d','e']);	// ell con dentro 2 el - array di array -> array con 2 el

		$ell5 = ell([['a','b','c'],['d','e']]);	// ell con dentro 2 el - array di array di array -> array con 2 el

		$ell6 = ell(el('a','b','c')); // ell con dentro 1 el - array di el -> array con 1 el

		$ell7 = ell(el('a','b','c'),el('d','e'));	// ell con dentro 2 el - array di el -> array con 2 el

		$ell8 = ell([el('a','b','c'),el('d','e')]);	// ell con dentro 2 el - array di array di el -> array con 2 el
		


		try {
			$this->assertEqual($ell1,"","La lista di liste non ritorna i dati attesi!");
			$this->fail("La lista di liste non lancia un'eccezione quando contiene stringhe!");
		} catch (\Exception $ex) {
			//ok
		}

		$this->assertEqual($ell3,"('a','b','c')","La lista di liste non ritorna i dati attesi!");
		
		$this->assertEqual($ell4,"('a','b','c'),('d','e')","La lista di liste non ritorna i dati attesi!");
		
		$this->assertEqual($ell5,"('a','b','c'),('d','e')","La lista di liste non ritorna i dati attesi!");
		
		$this->assertEqual($ell6,"('a','b','c')","La lista di liste non ritorna i dati attesi!");
		
		$this->assertEqual($ell7,"('a','b','c'),('d','e')","La lista di liste non ritorna i dati attesi!");
		
		$this->assertEqual($ell8,"('a','b','c'),('d','e')","La lista di liste non ritorna i dati attesi!");
		
	}	


}