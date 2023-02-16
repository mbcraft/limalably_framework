<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class MysqlQueryListTest extends LTestCase {
	
	function testQueryListFromStringWithIterator() {

		$db = db("hosting_dreamhost_tests");

		MysqlDbHelperTestLib::regenerateDb();

		delete('specie_albero')->go($db);

		delete('comune')->go($db);

		delete('provincia')->go($db);

		delete('regione')->go($db);


		$regione_id = insert('regione',['nome','codice'],['REGIONE_TEST','CODICE_REGIONE_TEST'])->go($db);
		
		$provincia_id = insert('provincia',['nome','codice','regione_id'],['PROVINCIA_TEST','CODICE_PROVINCIA_TEST',$regione_id])->go($db);

		$comune_id = insert('comune',['nome','codice','provincia_id'],['COMUNE_TEST','CODICE_COMUNE_TEST',$provincia_id])->go($db);
		
		$specie_id = insert('specie_albero',['nome'],['leccio'])->go($db);


		$ql = <<<END_OF_QUERY_LIST

UPDATE specie_albero SET nome = 'A';

SELECT * from specie_albero;

SELECT * from regione;

SELECT * from provincia;

SELECT * from comune;


END_OF_QUERY_LIST;

		$it = query_list($ql)->iterator($db);

		$this->assertTrue($it->hasNext(),"L'iteratore ha già finito i risultati!");
		$r = $it->next();
		$this->assertNull($r,"Il risultato ritornato dall'iteratore non è nullo!");
		$this->assertTrue($it->hasNext(),"L'iteratore ha già finito i risultati!");
		$r = $it->next();
		$this->assertTrue(is_array($r),"Il risultato ritornato dall'iteratore non è un array!");
		$this->assertTrue($it->hasNext(),"L'iteratore ha già finito i risultati!");
		$r = $it->next();
		$this->assertTrue(is_array($r),"Il risultato ritornato dall'iteratore non è un array!");
		
		$this->assertTrue($it->hasNext(),"L'iteratore ha già finito i risultati!");
		$r = $it->next();
		$this->assertTrue(is_array($r),"Il risultato ritornato dall'iteratore non è un array!");
		
		$this->assertTrue($it->hasNext(),"L'iteratore ha già finito i risultati!");
		$r = $it->next();
		$this->assertTrue(is_array($r),"Il risultato ritornato dall'iteratore non è un array!");
		
		$this->assertFalse($it->hasNext(),"L'iteratore ha altri risultati!");
	}


	function testQueryListFromStringWithFetchResults() {

		$db = db("hosting_dreamhost_tests");

		MysqlDbHelperTestLib::regenerateDb();

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

		$mq = new LMysqlQueryList($ql);

		$results = $mq->go($db);

		$this->assertEqual(4,count($results),"Il numero di risultati non corrisponde!");

		$this->assertEqual(1,count($results[0]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[1]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[2]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[3]),"Il numero di righe ritornate non corrisponde!");

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/db/query/mysql/multi_query_test.sql');

		$this->assertTrue("Il file di test delle multi query non è stato trovato.",$f->exists());
		$this->assertTrue("Il file di test delle multi query non ha i permessi di lettura.",$f->isReadable());

		$mqf = new LMysqlQueryList($ql);

		$results = $mqf->go($db);

		$this->assertEqual(4,count($results),"Il numero di risultati non corrisponde!");	

		$this->assertEqual(1,count($results[0]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[1]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[2]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[3]),"Il numero di righe ritornate non corrisponde!");
		
	}

	function testQueryListFromStringWithFetchResultsWithFunction() {

		$db = db("hosting_dreamhost_tests");

		MysqlDbHelperTestLib::regenerateDb();

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

		$mq = query_list($ql);

		$results = $mq->go($db);

		$this->assertEqual(4,count($results),"Il numero di risultati non corrisponde!");

		$this->assertEqual(1,count($results[0]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[1]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[2]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[3]),"Il numero di righe ritornate non corrisponde!");

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/db/query/mysql/multi_query_test.sql');

		$this->assertTrue("Il file di test delle multi query non è stato trovato.",$f->exists());
		$this->assertTrue("Il file di test delle multi query non ha i permessi di lettura.",$f->isReadable());

		$mqf = query_list_from_file($f);

		$results = $mqf->go($db);

		$this->assertEqual(4,count($results),"Il numero di risultati non corrisponde!");	

		$this->assertEqual(1,count($results[0]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[1]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[2]),"Il numero di righe ritornate non corrisponde!");
		$this->assertEqual(1,count($results[3]),"Il numero di righe ritornate non corrisponde!");

		
	}

	function testQueryListFromStringWithGoNoResults() {

		$db = db("hosting_dreamhost_tests");

		MysqlDbHelperTestLib::regenerateDb();

		$ql = <<<END_OF_QUERY_LIST

SELECT * from specie_albero;

SELECT * from regione;

SELECT * from provincia;

SELECT * from comune;


END_OF_QUERY_LIST;

		$mq = new LMysqlQueryList($ql);

		$mq->go_no_results($db);

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/db/query/mysql/multi_query_test.sql');

		$this->assertTrue("Il file di test delle multi query non è stato trovato.",$f->exists());
		$this->assertTrue("Il file di test delle multi query non ha i permessi di lettura.",$f->isReadable());

		$mqf = new LMysqlQueryList($f);

		$mqf->go_no_results($db);
	}

	function testQueryListFromFileFetchResults() {

		$db = db("hosting_dreamhost_tests");

		MysqlDbHelperTestLib::regenerateDb();

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

	function testQueryListFromFileNoResults() {

		$db = db("hosting_dreamhost_tests");

		MysqlDbHelperTestLib::regenerateDb();

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/db/query/mysql/multi_query_test.sql');

		$this->assertTrue("Il file di test delle multi query non è stato trovato.",$f->exists());
		$this->assertTrue("Il file di test delle multi query non ha i permessi di lettura.",$f->isReadable());

		$mq = new LMysqlQueryList($f);

		$mq->go_no_results($db);
		
	}

}