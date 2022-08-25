<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class SelectStatementTest extends LTestCase {
	
	function testBasicSelect() {
		
		$db = db("framework_unit_tests");

		$s1 = select('*','mia_tabella');
		$s2 = select('*','mia_tabella',_eq('a',1));
		$s3 = select('*','mia_tabella')->where(_eq('a',1));
		$s4 = select('*','mia_tabella')->left_join('join_tab',_eq('c',2))->where(_eq('a',1));
		$s5 = select('*','mia_tabella')->left_join('join_tab',_eq('c',2))->where(_eq('a',1))->group_by('nome_campo')->having(_eq('a.b',12));
		$s6 = select('*','mia_tabella')->order_by(asc('nome_campo1'),desc('nome_campo2'))->paginate(3,1);


		$this->assertEqual($s1,"SELECT * FROM mia_tabella","L'SQL della select non corrisponde a quello atteso!");
		$this->assertEqual($s2,"SELECT * FROM mia_tabella WHERE a = 1","L'SQL della select non corrisponde a quello atteso!");
		$this->assertEqual($s3,"SELECT * FROM mia_tabella WHERE a = 1","L'SQL della select non corrisponde a quello atteso!");
		$this->assertEqual($s4,"SELECT * FROM mia_tabella LEFT JOIN join_tab ON c = 2 WHERE a = 1","L'SQL della select non corrisponde a quello atteso!");
		$this->assertEqual($s5,"SELECT * FROM mia_tabella LEFT JOIN join_tab ON c = 2 WHERE a = 1 GROUP BY nome_campo HAVING a.b = 12","L'SQL della select non corrisponde a quello atteso!");
		$this->assertEqual($s6,"SELECT * FROM mia_tabella ORDER BY nome_campo1 ASC,nome_campo2 DESC LIMIT 0,3","L'SQL della select non corrisponde a quello atteso!");
		
	}

	function testSelectInFrom() {

		$db = db("framework_unit_tests");

		truncate('targhetta_albero')->go($db);

		insert('targhetta_albero',['codice_targhetta'],['comune'])->go($db);

		select('*',tn(select('codice_targhetta','targhetta_albero'),'mia_tabella'))->go($db);

	}

}