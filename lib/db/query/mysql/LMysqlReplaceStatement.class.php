<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlReplaceStatement  extends LMysqlAbstractInsertOrReplaceStatement {

	protected function statement_name() {
		return "replace";
	}

	public function __toString() {

		if ($this->data instanceof LMysqlSelectStatement) {
			return $this->build_query("REPLACE INTO",$this->table_name,$this->column_list->toRawStringList(),$this->data);
		}
		if (!$this->column_list) {
			if ($this->data instanceof LMysqlNameValuePairList) {
				return $this->build_query("REPLACE INTO",$this->table_name,"SET",$this->data);
			} 
		}

		return $this->build_query("REPLACE INTO",$this->table_name,$this->column_list->toRawStringList(),"VALUES",$this->data);
	}

}