<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

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

	function testSomeMoreQueries() {

		$db = db('framework_unit_tests');

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
		
		$i_query = insert('check_up_albero',['albero_id','data','esito'],[[$albero_id,'2022-08-20',1],[$albero_id,'2022-08-20',2],[$albero_id,'2022-08-20',3],[$albero_id,'2022-08-20',4],[$albero_id,'2022-08-20',5]]);

		//echo $i_query;

		$i_query->go($db);

		$r_qs1 = select('count(*) AS C','check_up_albero')->go($db);

		$this->assertEqual($r_qs1[0]['C'],5,"Il numero di righe nella tabella check_up_albero non corrisponde a quelle attese!");

		$qs2 = select(['a.latitudine,a.longitudine,cua.data,cua.esito'],'albero a')->left_join('check_up_albero cua',_eq('cua.albero_id',f('a.id')))->order_by(asc('data'))->paginate(2,1);

		//echo $qs2;

		$result = $qs2->go($db);

		$this->assertEqual(count($result),2,"Il numero di righe ritornate dalla select non corrisponde a quelle attese!");

		//delete('check_up_albero')->go($db);

		//delete('albero')->go($db);

		//delete('specie_albero')->go($db);

		//delete('comune')->go($db);

		//delete('provincia')->go($db);

		//delete('regione')->go($db);

	}

}