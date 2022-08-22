<?php


class TableDescriptionTest extends LTestCase {
	

	function testTableDescription() {


		$db = db('framework_unit_tests');


		$td = table_description('regione')->go($db);

		$this->assertEqual(count($td),3,"Il numero dei campi della descrizione della tabella 'regione' non corrisponde!");

		$r1 = $td[0];
		$r2 = $td[1];
		$r3 = $td[2];

		$this->assertEqual($r1->getColumnName(),"id","Il nome del campo non corrisponde!");
		$this->assertEqual($r2->getColumnName(),"nome","Il nome del campo non corrisponde!");
		$this->assertEqual($r3->getColumnName(),"codice","Il nome del campo non corrisponde!");

		$this->assertEqual($r1->getColumnType(),"bigint unsigned","Il tipo del campo non corrisponde!");
		$this->assertEqual($r2->getColumnType(),"varchar(32)","Il tipo del campo non corrisponde!");
		$this->assertEqual($r3->getColumnType(),"varchar(32)","Il tipo del campo non corrisponde!");

		$this->assertEqual($r1->isNull(),"NO","Il null del campo non corrisponde!");
		$this->assertEqual($r2->isNull(),"NO","Il null del campo non corrisponde!");
		$this->assertEqual($r3->isNull(),"NO","Il null del campo non corrisponde!");
			
	}

}