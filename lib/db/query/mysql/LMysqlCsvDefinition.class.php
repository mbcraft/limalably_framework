<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlCsvDefinition {
	
	private $csv_path;

	private $fields_incipit = "";
	private $fields_terminated_by = null;
	private $fields_enclosed_by = null;
	private $fields_escaped_by = null;

	private $lines_terminated_by = null;

	function __construct($csv_file_or_path) {

		if ($csv_file_or_path instanceof LFile) {
			$this->csv_path = $csv_file_or_path->getFullPath();
		} else {

			$this->csv_path = $csv_file_or_path;

		}

	}

	protected function build_query(... $parts) {

		$final_part_list = [];
		foreach ($parts as $p) {
			if ($p == null || trim("".$p) == null) continue;
			$final_part_list [] = $p;
		}
		return implode(' ',$final_part_list);
	}

	private function escape_characters($string) {
		return str_replace("'","\'",$string);
	}

	public function fields_terminated_by(string $st) 
	{
		$this->fields_incipit = "FIELDS";

		$this->fields_terminated_by = "TERMINATED BY '".$this->escape_characters($st)."'";

		return $this;
	}

	public function fields_enclosed_by(string $st) {

		$this->fields_incipit = "FIELDS";

		$this->fields_enclosed_by = "ENCLOSED BY '".$this->escape_characters($st)."'";

		return $this;
	}

	public function fields_escaped_by(string $st) {

		$this->fields_incipit = "FIELDS";

		$this->fields_escaped_by = "ESCAPED BY '".$this->escape_characters($st)."'";

		return $this;
	}

	public function lines_terminated_by(string $st) {
		$this->lines_terminated_by = "LINES TERMINATED BY '".$this->escape_characters($st)."'";

		return $this;
	}

	public function __write_header() {
		return "INTO OUTFILE '".$this->csv_path."'";
	}

	public function __read_header() {
		return "LOAD DATA LOCAL INFILE '".$this->csv_path."'";
	}

	public function __trailer() {
		return $this->build_query($this->fields_incipit,$this->fields_escaped_by,$this->fields_enclosed_by,$this->fields_terminated_by,$this->lines_terminated_by);
	}

}