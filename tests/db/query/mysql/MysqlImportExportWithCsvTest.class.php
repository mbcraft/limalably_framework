<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MysqlImportExportWithCsvTest extends LTestCase {
	

	function testImportExportWithCsv() {

		try {
			$this->executeTests();
		} catch (\Exception $ex) {
			echo "\n\nSkipping mysql csv tests due to database server filesystem not writable ...\n\n";
		}

	}

	function executeTests() {
		$db = db('hosting_dreamhost_tests');

		drop_table('my_test')->if_exists()->go($db);

		create_table('my_test')->column(col_def('id')->t_id())->column(col_def('testo')->t_text32())->column(col_def('valore_int')->t_u_int())->go($db);

		insert('my_test',['testo','valore_int'],['abcd1',12])->go($db);

		insert('my_test')->column_list('testo','valore_int')->data(['abcd2',34])->go($db);

		insert('my_test')->column_list(['testo','valore_int'])->data(['abcd2',12])->go($db);

		insert('my_test')->column_list(['testo','valore_int'])->data([['abcd2',12],['abcd3',34]])->go($db);

		$f = new LFile($_SERVER['FRAMEWORK_DIR']."/tests/db/query/mysql/export_my_test_table.csv");

		if ($f->exists()) $f->delete();

		$csv_def = csv_def($f)->fields_enclosed_by("'")->fields_terminated_by(',')->lines_terminated_by("\r\n");

		select('*','my_test')->export_to_csv($csv_def)->go($db);

		$this->assertTrue($f->exists(),"Il file csv con l'export dei dati non è stato creato!");

		$this->assertTrue($f->getSize()>0,"Il file csv con l'export dei dati risulta essere vuoto!");

		$r = select('count(*) AS C','my_test')->go($db);

		$this->assertEqual($r['C'],5,"La tabella contiene ancora dei dati!");

		truncate('my_test')->go($db);

		$r = select('count(*) AS C','my_test')->go($db);

		$this->assertEqual($r['C'],0,"La tabella contiene ancora dei dati!");

		import_csv_into_table('my_test',$csv_def)->go($db);

		$r = select('count(*) AS C','my_test')->go($db);

		$this->assertEqual($r['C'],5,"La tabella contiene ancora dei dati!");

	}
	


}