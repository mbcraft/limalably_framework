<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlUpdateStatement extends LMysqlAbstractQuery
{

	private $table_name;
	private $name_value_pair_list;
	private $where_block;

	public function __construct($table_name,$name_value_pair_list,$where_block=null) {
		
		if (!is_string($table_name)) throw new \Exception("The table name of the update statement is not a string.");
		$this->table_name = $table_name;
		
		if (is_array($name_value_pair_list)) $name_value_pair_list = new LMysqlNameValuePairList($name_value_pair_list);
		else ensure_instance_of("name value pair list in update statament",$name_value_pair_list,[LMysqlNameValuePairList::class]);
		$this->name_value_pair_list = $name_value_pair_list;

		if ($where_block!=null) {
			if (!$where_block instanceof LMysqlWhereBlock) $where_block = new LMysqlWhereBlock($where_block);

			ensure_instance_of("where condition of mysql update statement",$where_block,[LMysqlWhereBlock::class]);
			
			$this->where_block = $where_block;
		} else {
			$this->where_block = "";
		}
		
		
	}

	public function where(... $elements) {

		if (count($elements)==1 && is_array($elements[0])) {
			$this->where_block = new LMysqlWhereBlock($elements[0]);
		} else {
			$this->where_block = new LMysqlWhereBlock($elements);
		}
		
		return $this;
	}

	public function __toString() {

		return $this->build_query("UPDATE",$this->table_name,"SET",$this->name_value_pair_list,$this->where_block);

	}

}