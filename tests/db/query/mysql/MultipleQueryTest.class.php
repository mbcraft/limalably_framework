<?php


class MultipleQueryTest extends LTestCase {
	

	function testSomeLittleQueries() {

		$db = db('framework_unit_tests');

		delete('regione')->go($db);

		$this->assertEqual(0,select('count(*) AS C','regione')->go($db)[0]['C'],"Il numero dei valori all'interno della tabella non corrisponde!");

		$result = insert('regione',['nome','codice'],[['regione1','C-R1'],['regione2','C-R2']])->go($db);

		$this->assertTrue($result!=0,"Il numero di righe inserite non corrisponde a quello atteso!");

		$this->assertEqual(2,select('count(*) AS C','regione')->go($db)[0]['C'],"Il numero dei valori all'interno della tabella non corrisponde!");

		delete('regione')->go($db);

		$this->assertEqual(0,select('count(*) AS C','regione')->go($db)[0]['C'],"Il numero dei valori all'interno della tabella non corrisponde!");

	}

}