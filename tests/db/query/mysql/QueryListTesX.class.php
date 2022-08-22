<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class QueryListTesX extends LTestCase {
	


	function testMultiQueryFromFile() {

		

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].'tests/db/query/mysql/multi_query_test.sql');

		$this->assertTrue("Il file di test delle multi query non è stato trovato.",$f->exists());
		$this->assertTrue("Il file di test delle multi query non ha i permessi di lettura.",$f->isReadable());

		$db = db("framework_unit_tests");

		$mq = new LMysqlQueryList($f);

		$mq->go($db);

		
	}

}