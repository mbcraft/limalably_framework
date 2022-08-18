<?php


/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class MultiQueryTest extends LTestCase {
	


	function testMultiQueryFromFile() {

		$f = new LFile($_SERVER['PROJECT_DIR'].'tests/db/query/multi_query_test.sql');

		$this->assertTrue("Il file di test delle multi query non è stato trovato.",$f->exists());
		$this->assertTrue("Il file di test delle multi query non ha i permessi di lettura.",$f->isReadable());

		$db = db("unit_tests");

		$mq = new LMysqlMultiQuery($f);

		$mq->go($db);
	}

}