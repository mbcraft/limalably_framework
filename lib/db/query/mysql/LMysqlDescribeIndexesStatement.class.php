<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/


class LMysqlDescribeIndexesStatement extends LMysqlAbstractQuery {
	
	private $table_name;

	function __construct($table_name) {
		$this->table_name = $table_name;
	}

	function __toString() {

		return "SHOW INDEX FROM ".$this->table_name;

	}

	public function go($connection) {

		$full_result = parent::go($connection);

		$ix_result = [];

		foreach ($full_result as $row) {
			$ix_result []= new LMysqlIndexDescription($row['Table'],$row['Non_unique'],$row['Key_name'],$row['Seq_in_index'],$row['Column_name'],$row['Collation'],$row['Cardinality'],$row['Sub_part'],$row['Packed'],$row['Null'],$row['Index_type'],$row['Comment'],$row['Index_comment'],$row['Visible'],$row['Expression']);
		}


		return $ix_result;
	}

	public function iterator($connection) {
		throw new \Exception("iterator function is not supported for this statement.Use 'go' and get the full result");
	}


}