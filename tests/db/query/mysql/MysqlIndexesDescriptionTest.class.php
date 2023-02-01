<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MysqlIndexesDescriptionTest extends LTestCase {
	


	function testDescribeIndexes() {


		$db = db('hosting_dreamhost_tests');

		MysqlDbHelperTestLib::regenerateDb();

		$indexes = table_indexes_list('albero')->go($db);

		$this->assertEqual(count($indexes),1,"Il numero di indici trovati non corrisponde!");

		foreach ($indexes as $ixd) {
			$this->assertTrue($ixd instanceof LIIndexDescription,"L'indice non è nell'oggetto corretto!");
		}

		$indexes = table_indexes_list('provincia')->go($db);

		$this->assertEqual(count($indexes),1,"Il numero di indici trovati non corrisponde!");

		foreach ($indexes as $ixd) {
			$this->assertTrue($ixd instanceof LIIndexDescription,"L'indice non è nell'oggetto corretto!");
		}


	}


}