<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class MysqlResultIteratorTest extends LTestCase {


	function testResultIteratorWithSomeQueries() {


		$db = db('hosting_dreamhost_tests');

		delete('check_up_albero')->go($db);

		delete('albero')->go($db);

		delete('specie_albero')->go($db);

		delete('comune')->go($db);

		delete('provincia')->go($db);

		delete('regione')->go($db);

		
		$regione_id = insert('regione',['nome','codice'],['REGIONE_TEST','CODICE_REGIONE_TEST'])->go($db);
		
		$provincia_id = insert('provincia',['nome','codice','regione_id'],['PROVINCIA_TEST','CODICE_PROVINCIA_TEST',$regione_id])->go($db);

		$comune_id = insert('comune',['nome','codice','provincia_id'],['COMUNE_TEST','CODICE_COMUNE_TEST',$provincia_id])->go($db);
		
		$specie_id = insert('specie_albero',['nome'],['leccio'])->go($db);

		$albero_id = insert('albero',['data_piantumazione','latitudine','longitudine','specie_albero_id','comune_id'],['2022-08-20',44.4105672,12.0095168,$specie_id,$comune_id])->go($db);
		
		$i_query = insert('check_up_albero',['albero_id','data','esito'],[[$albero_id,'2022-08-20',1],[$albero_id,'2022-08-20',2],[$albero_id,'2022-08-20',3],[$albero_id,'2022-08-20',4],[$albero_id,'2022-08-20',5]])->go($db);

		$it = select('*','check_up_albero')->iterator($db);

		$this->assertTrue($it->hasNext(),"Non ci sono più risultati da leggere!");
		$this->assertEqual(count($it->next()),4,"La riga non è con il numero di campi attesi!");
		$this->assertTrue($it->hasNext(),"Non ci sono più risultati da leggere!");
		$this->assertEqual(count($it->next()),4,"La riga non è con il numero di campi attesi!");
		$this->assertTrue($it->hasNext(),"Non ci sono più risultati da leggere!");
		$this->assertEqual(count($it->next()),4,"La riga non è con il numero di campi attesi!");
		$this->assertTrue($it->hasNext(),"Non ci sono più risultati da leggere!");
		$this->assertEqual(count($it->next()),4,"La riga non è con il numero di campi attesi!");
		$this->assertTrue($it->hasNext(),"Non ci sono più risultati da leggere!");
		$this->assertEqual(count($it->next()),4,"La riga non è con il numero di campi attesi!");
		$this->assertFalse($it->hasNext(),"Ci sono altri risultati da leggere nell'iteratore ma non ce ne dovrebbero essere!");
		
	}


}