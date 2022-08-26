<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class IndexesDescriptionTest extends LTestCase {
	


	function testDescribeIndexes() {


		$db = db('framework_unit_tests');

		$indexes = describe_indexes('albero')->go($db);

		$this->assertEqual(count($indexes),3,"Il numero di indici trovati non corrisponde!");

		foreach ($indexes as $ixd) {
			$this->assertTrue($ixd instanceof LIIndexDescription,"L'indice non è nell'oggetto corretto!");
		}


	}


}