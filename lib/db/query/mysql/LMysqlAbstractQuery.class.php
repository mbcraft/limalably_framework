<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

abstract class LMysqlAbstractQuery {

	protected function ensure_valid_order_by_element($order_by_element) {

		if (!is_string($order_by_element)) throw new \Exception("The order by element is not a string in the mysql select clause.");

		$lowered = strtolower($order_by_element);
		$parts = explode(' ',$order_by_element);

		if (count($parts)>2) throw new \Exception("The order by element is not made of two space separated strings.");
		if (count($parts)==2) {
			$order_descriptor = $parts[1];

			if ($order_descriptor!='ASC' && $order_descriptor!='DESC') throw new \Exception("Order descriptor is neither 'asc' or 'desc' in mysql select order by clause.");
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

	function go($connection) {

		if (!$connection) throw new \Exception("Connection is not set!");

		if (!$connection->isOpen()) $connection->open();

		$connection_handle = $connection->getHandle();

		$result = mysqli_query($connection_handle,$this->end());

		if (!$result) throw new \Exception("Mysql query failed : ".mysqli_error($connection_handle));
		
		if ($this instanceof LMysqlInsertStatement) {
			
			return mysqli_insert_id($connection_handle);
			
		}
		if ($this instanceof LMysqlSelectStatement || $this instanceof LMysqlTableDescriptionStatement || $this instanceof LMysqlDescribeIndexesStatement || $this instanceof LMysqlShowPrivilegesStatement) {
			$full_result = [];

			while ($row = mysqli_fetch_assoc($result)) $full_result[] = $row;

			return $full_result;
			
		}
	}

	function end() {
		return $this.";";
	}

	function iterator($connection) {

		if (!$connection) throw new \Exception("Connection is not set!");

		if (!$connection->isOpen()) $connection->open();

		$connection_handle = $connection->getHandle();

		$result = mysqli_query($connection_handle,$this->end(),MYSQLI_USE_RESULT);

		return new LMysqlStandardResultIterator($result);
		
	}

}