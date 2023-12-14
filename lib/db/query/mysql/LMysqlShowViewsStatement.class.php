<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LMysqlShowViewsStatement extends LMysqlAbstractQuery {
	
	public function __toString() {
		return $this->build_query("SHOW FULL TABLES","WHERE","table_type='VIEW'");
	}

	public function extractResults($result) {

		$final_result = [];

		foreach ($result as $row) {
			foreach ($row as $key => $value) {
				if (strpos($key,'Tables_in')===0) $final_result[] = $value;
			}
		}

		return $final_result;
	}

}