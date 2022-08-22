<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class QueryListTest extends LTestCase {
	


	function testMultiQueryFromStringWithFetchResults() {

		$db = db("framework_unit_tests");

		delete('specie_albero')->go($db);

		delete('comune')->go($db);

		delete('provincia')->go($db);

		delete('regione')->go($db);


		$regione_id = insert('regione',['nome','codice'],['REGIONE_TEST','CODICE_REGIONE_TEST'])->go($db);
		
		$provincia_id = insert('provincia',['nome','codice','regione_id'],['PROVINCIA_TEST','CODICE_PROVINCIA_TEST',$regione_id])->go($db);

		$comune_id = insert('comune',['nome','codice','provincia_id'],['COMUNE_TEST','CODICE_COMUNE_TEST',$provincia_id])->go($db);
		
		$specie_id = insert('specie_albero',['nome'],['leccio'])->go($db);


		$ql = <<<END_OF_QUERY_LIST

SELECT * from specie_albero;

SELECT * from regione;

SELECT * from provincia;

SELECT * from comune;


END_OF_QUERY_LIST;

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/db/query/mysql/multi_query_test.sql');

		$this->assertTrue("Il file di test delle multi query non è stato trovato.",$f->exists());
		$this->assertTrue("Il file di test delle multi query non ha i permessi di lettura.",$f->isReadable());

		$mq = new LMysqlQueryList($ql);

		$results = $mq->go($db);

		$this->assertEqual(4,count($results),"Il numero di risultati non corrisponde!");

		$this->assertEqual(1,count($results[0]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[1]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[2]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[3]),"Il numero di righe ritornate non corrisponde!");

		
	}

	function testMultiQueryFromStringWithGoNoResults() {

		$db = db("framework_unit_tests");

		$ql = <<<END_OF_QUERY_LIST

SELECT * from specie_albero;

SELECT * from regione;

SELECT * from provincia;

SELECT * from comune;


END_OF_QUERY_LIST;

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/db/query/mysql/multi_query_test.sql');

		$this->assertTrue("Il file di test delle multi query non è stato trovato.",$f->exists());
		$this->assertTrue("Il file di test delle multi query non ha i permessi di lettura.",$f->isReadable());

		$mq = new LMysqlQueryList($ql);

		$mq->go_no_results($db);

		
	}

	function testMultiQueryFromFileFetchResults() {

		$db = db("framework_unit_tests");

		delete('specie_albero')->go($db);

		delete('comune')->go($db);

		delete('provincia')->go($db);

		delete('regione')->go($db);

		$regione_id = insert('regione',['nome','codice'],['REGIONE_TEST','CODICE_REGIONE_TEST'])->go($db);
		
		$provincia_id = insert('provincia',['nome','codice','regione_id'],['PROVINCIA_TEST','CODICE_PROVINCIA_TEST',$regione_id])->go($db);

		$comune_id = insert('comune',['nome','codice','provincia_id'],['COMUNE_TEST','CODICE_COMUNE_TEST',$provincia_id])->go($db);
		
		$specie_id = insert('specie_albero',['nome'],['leccio'])->go($db);

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/db/query/mysql/multi_query_test.sql');

		$this->assertTrue("Il file di test delle multi query non è stato trovato.",$f->exists());
		$this->assertTrue("Il file di test delle multi query non ha i permessi di lettura.",$f->isReadable());

		$mq = new LMysqlQueryList($f);

		$results = $mq->go($db);

		$this->assertEqual(4,count($results),"Il numero di risultati non corrisponde!");

		$this->assertEqual(1,count($results[0]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[1]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[2]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[3]),"Il numero di righe ritornate non corrisponde!");
		
	}

	function testMultiQueryFromFileNoResults() {

		$db = db("framework_unit_tests");

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/db/query/mysql/multi_query_test.sql');

		$this->assertTrue("Il file di test delle multi query non è stato trovato.",$f->exists());
		$this->assertTrue("Il file di test delle multi query non ha i permessi di lettura.",$f->isReadable());

		$mq = new LMysqlQueryList($f);

		$mq->go_no_results($db);
		
	}

}