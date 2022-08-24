<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlImportCsvIntoTableStatement extends LMysqlAbstractQuery {
	

	private $table_name;
	private $ignore_rows = "";
	private $csv_def = null;

	function __construct(string $table_name,$csv_def) {
		$this->table_name = $table_name;

		if (!$csv_def instanceof LMysqlCsvDefinition) throw new \Exception("Csv definition is not valid!");

		$this->csv_def = $csv_def;
	}

	public function __toString() {

		return $this->build_query($this->csv_def->__read_header(),"INTO","TABLE",$this->table_name,$this->csv_def->__trailer(),$this->ignore_rows);
	}

	public function ignore_rows(int $num) {
		$this->ignore_rows = "IGNORE ".$num." ROWS";

		return $this;
	}

}
