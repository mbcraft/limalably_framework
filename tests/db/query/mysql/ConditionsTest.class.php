<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class ConditionsTest extends LTestCase {
	


	function testEqual() {

		db('framework_unit_tests');

		$c1 = _eq('field_name',123);
		$c2 = _eq('field_name',12.34);
		$c3 = _eq('field_name','myvalue');

		$this->assertEqual($c1,"field_name = 123","Il valore della _eq non corrisponde a quello atteso!");
		$this->assertEqual($c2,"field_name = 12.34","Il valore della _eq non corrisponde a quello atteso!");
		$this->assertEqual($c3,"field_name = 'myvalue'","Il valore della _eq non corrisponde a quello atteso!");

		$c4 = _equal('field_name',123);
		$c5 = _equal('field_name',12.34);
		$c6 = _equal('field_name','myvalue');

		$this->assertEqual($c4,"field_name = 123","Il valore della _equal non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name = 12.34","Il valore della _equal non corrisponde a quello atteso!");
		$this->assertEqual($c6,"field_name = 'myvalue'","Il valore della _equal non corrisponde a quello atteso!");
	}

	function testNotEqual() {

		db('framework_unit_tests');

		$c1 = _n_eq('field_name',123);
		$c2 = _n_eq('field_name',12.34);
		$c3 = _n_eq('field_name','myvalue');

		$this->assertEqual($c1,"field_name != 123","Il valore della _n_eq non corrisponde a quello atteso!");
		$this->assertEqual($c2,"field_name != 12.34","Il valore della _n_eq non corrisponde a quello atteso!");
		$this->assertEqual($c3,"field_name != 'myvalue'","Il valore della _n_eq non corrisponde a quello atteso!");

		$c4 = _not_equal('field_name',123);
		$c5 = _not_equal('field_name',12.34);
		$c6 = _not_equal('field_name','myvalue');

		$this->assertEqual($c4,"field_name != 123","Il valore della _not_equal non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name != 12.34","Il valore della _not_equal non corrisponde a quello atteso!");
		$this->assertEqual($c6,"field_name != 'myvalue'","Il valore della _not_equal non corrisponde a quello atteso!");
	}

	function testGreaterThan() {

		db('framework_unit_tests');

		$c1 = _gt('field_name',123);
		$c2 = _gt('field_name',12.34);

		$this->assertEqual($c1,"field_name > 123","Il valore della _gt non corrisponde a quello atteso!");
		$this->assertEqual($c2,"field_name > 12.34","Il valore della _gt non corrisponde a quello atteso!");

		$c4 = _greater_than('field_name',123);
		$c5 = _greater_than('field_name',12.34);

		$this->assertEqual($c4,"field_name > 123","Il valore della _greater_than non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name > 12.34","Il valore della _greater_than non corrisponde a quello atteso!");
	}

	function testGreaterThanOrEqual() {

		db('framework_unit_tests');

		$c1 = _gt_eq('field_name',123);
		$c2 = _gt_eq('field_name',12.34);

		$this->assertEqual($c1,"field_name >= 123","Il valore della _gt_eq non corrisponde a quello atteso!");
		$this->assertEqual($c2,"field_name >= 12.34","Il valore della _gt_eq non corrisponde a quello atteso!");

		$c4 = _greater_than_or_equal('field_name',123);
		$c5 = _greater_than_or_equal('field_name',12.34);

		$this->assertEqual($c4,"field_name >= 123","Il valore della _greater_than_or_equal non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name >= 12.34","Il valore della _greater_than_or_equal non corrisponde a quello atteso!");
	}

	function testLessThan() {

		db('framework_unit_tests');

		$c1 = _lt('field_name',123);
		$c2 = _lt('field_name',12.34);

		$this->assertEqual($c1,"field_name < 123","Il valore della _lt non corrisponde a quello atteso!");
		$this->assertEqual($c2,"field_name < 12.34","Il valore della _lt non corrisponde a quello atteso!");

		$c4 = _less_than('field_name',123);
		$c5 = _less_than('field_name',12.34);

		$this->assertEqual($c4,"field_name < 123","Il valore della _less_than non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name < 12.34","Il valore della _less_than non corrisponde a quello atteso!");
	}

	function testLessThanOrEqual() {

		db('framework_unit_tests');

		$c1 = _lt_eq('field_name',123);
		$c2 = _lt_eq('field_name',12.34);

		$this->assertEqual($c1,"field_name <= 123","Il valore della _lt_eq non corrisponde a quello atteso!");
		$this->assertEqual($c2,"field_name <= 12.34","Il valore della _lt_eq non corrisponde a quello atteso!");

		$c4 = _less_than_or_equal('field_name',123);
		$c5 = _less_than_or_equal('field_name',12.34);

		$this->assertEqual($c4,"field_name <= 123","Il valore della _less_than_or_equal non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name <= 12.34","Il valore della _less_than_or_equal non corrisponde a quello atteso!");
	}

	function testLike() {

		db('framework_unit_tests');

		$c4 = _like('field_name','abc');
		$c5 = _like('field_name','%abc%');

		$this->assertEqual($c4,"field_name LIKE 'abc'","Il valore della _like non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name LIKE '%abc%'","Il valore della _like non corrisponde a quello atteso!");
	}

	function testNotLike() {

		db('framework_unit_tests');

		$c4 = _not_like('field_name','abc');
		$c5 = _not_like('field_name','%abc%');

		$this->assertEqual($c4,"field_name NOT LIKE 'abc'","Il valore della _not_like non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name NOT LIKE '%abc%'","Il valore della _not_like non corrisponde a quello atteso!");
	}

	function testRLike() {

		db('framework_unit_tests');

		$c4 = _rlike('field_name','abc');
		$c5 = _rlike('field_name','def');

		$this->assertEqual($c4,"field_name RLIKE 'abc'","Il valore della _rlike non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name RLIKE 'def'","Il valore della _rlike non corrisponde a quello atteso!");
	}

	function testIsNull() {

		db('framework_unit_tests');

		$c1 = _is_null('field_name');
		$c2 = _is_null('field123');

		$this->assertEqual($c1,"field_name IS NULL","Il valore della _is_null non corrisponde a quello atteso!");
		$this->assertEqual($c2,"field123 IS NULL","Il valore della _is_null non corrisponde a quello atteso!");


	}

	function testIsNotNull() {

		db('framework_unit_tests');

		$c1 = _is_not_null('field_name');
		$c2 = _is_not_null('field123');

		$this->assertEqual($c1,"field_name IS NOT NULL","Il valore della _is_not_null non corrisponde a quello atteso!");
		$this->assertEqual($c2,"field123 IS NOT NULL","Il valore della _is_not_null non corrisponde a quello atteso!");


	}

	function testIn() {

		db('framework_unit_tests');

		$c1 = _in('field_name',[]);
		$c2 = _in('field_name',[1,2,3]);
		$c3 = _in('field_name',['ab','cd','ef']);
		$c4 = _in('field_name',['ab',12]);
		$c5 = _in('field_name',el('ab',12));
		$c6 = _in('field_name',select('*','mia_tabella'));

		$this->assertEqual($c1,"field_name IN ('!')","Il valore della _in non corrisponde a quello atteso!");
		$this->assertEqual($c2,"field_name IN (1,2,3)","Il valore della _in non corrisponde a quello atteso!");
		$this->assertEqual($c3,"field_name IN ('ab','cd','ef')","Il valore della _in non corrisponde a quello atteso!");
		$this->assertEqual($c4,"field_name IN ('ab',12)","Il valore della _in non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name IN ('ab',12)","Il valore della _in non corrisponde a quello atteso!");
		$this->assertEqual($c6,"field_name IN ( SELECT * FROM mia_tabella )","Il valore della _not_in non corrisponde a quello atteso!");

	}


	function testNotIn() {

		db('framework_unit_tests');

		$c1 = _not_in('field_name',[]);
		$c2 = _not_in('field_name',[1,2,3]);
		$c3 = _not_in('field_name',['ab','cd','ef']);
		$c4 = _not_in('field_name',['ab',12]);
		$c5 = _not_in('field_name',el('ab',12));
		$c6 = _not_in('field_name',select('*','mia_tabella'));

		$this->assertEqual($c1,"field_name NOT IN ('!')","Il valore della _not_in non corrisponde a quello atteso!");
		$this->assertEqual($c2,"field_name NOT IN (1,2,3)","Il valore della _not_in non corrisponde a quello atteso!");
		$this->assertEqual($c3,"field_name NOT IN ('ab','cd','ef')","Il valore della _not_in non corrisponde a quello atteso!");
		$this->assertEqual($c4,"field_name NOT IN ('ab',12)","Il valore della _not_in non corrisponde a quello atteso!");
		$this->assertEqual($c5,"field_name NOT IN ('ab',12)","Il valore della _not_in non corrisponde a quello atteso!");
		$this->assertEqual($c6,"field_name NOT IN ( SELECT * FROM mia_tabella )","Il valore della _not_in non corrisponde a quello atteso!");

	}

	function testBetween() {

		db('framework_unit_tests');

		$c1 = _between('field_name',12,34);

		$this->assertEqual($c1,"field_name BETWEEN 12 AND 34","Il valore della _between non corrisponde a quello atteso!");

		$c2 = _bt('field_name',12,34);

		$this->assertEqual($c2,"field_name BETWEEN 12 AND 34","Il valore della _bt non corrisponde a quello atteso!");
	}

	function testNotBetween() {
		db('framework_unit_tests');

		$c1 = _not_between('field_name',12,34);

		$this->assertEqual($c1,"field_name NOT BETWEEN 12 AND 34","Il valore della _not_between non corrisponde a quello atteso!");

		$c2 = _n_bt('field_name',12,34);

		$this->assertEqual($c1,"field_name NOT BETWEEN 12 AND 34","Il valore della _n_bt non corrisponde a quello atteso!");
	}

	function testExists() {
		db('framework_unit_tests');

		$c1 = _exists(select('*','mia_tabella'));

		$this->assertEqual($c1,"EXISTS( SELECT * FROM mia_tabella )","Il valore della _exists non corrisponde a quello atteso!");
	}

	function testNotExists() {
		db('framework_unit_tests');

		$c1 = _not_exists(select('*','mia_tabella'));

		$this->assertEqual($c1,"NOT EXISTS( SELECT * FROM mia_tabella )","Il valore della _not_exists non corrisponde a quello atteso!");
	}

	function testAnd() {

		db('framework_unit_tests');

		$c1 = _and(_eq('a',1),_eq('b','z'));

		$this->assertEqual($c1,"( a = 1 AND b = 'z' )","Il valore della _and non corrisponde a quello atteso!");

	}

	function testOr() {

		db('framework_unit_tests');

		$c1 = _or(_eq('a',1),_eq('b','z'));

		$this->assertEqual($c1,"( a = 1 OR b = 'z' )","Il valore della _or non corrisponde a quello atteso!");

	}


}