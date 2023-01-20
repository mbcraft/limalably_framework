<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlCondition {

	private $parts;

	private function __construct(... $parts) {
		$this->parts = $parts;
	}

	public function __toString() {
		return implode(' ',$this->parts);
	}

	public static function is_null($column_name) {
		ensure_string_not_null("mysql 'is null' condition",$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'IS','NULL');
	}

	public static function is_not_null($column_name) {
		ensure_string_not_null("mysql 'is not null' condition",$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'IS','NOT','NULL');
	}

	public static function equal($column_name,$column_value) {
		ensure_string_not_null("mysql 'equal' condition",$column_name);
		if ($column_value===null) throw new \Exception("Column value is null with column ".$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'=',new LMysqlValueRenderer($column_value));
	}

	public static function equal_null($column_name,$column_value) {
		ensure_string_not_null("mysql 'equal' condition",$column_name);
		if ($column_value===null)
			return new LMysqlCondition(new LMysqlColumnName($column_name),'IS','NULL');
		else
			return new LMysqlCondition(new LMysqlColumnName($column_name),'=',new LMysqlValueRenderer($column_value));
	}

	public static function not_equal($column_name,$column_value) {
		ensure_string_not_null("mysql 'not equal' condition",$column_name);
		if ($column_value===null) throw new \Exception("Column value is null with column ".$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'!=',new LMysqlValueRenderer($column_value));
	}

	public static function not_equal_null($column_name,$column_value) {
		ensure_string_not_null("mysql 'not equal' condition",$column_name);
		if ($column_value===null)
			return new LMysqlCondition(new LMysqlColumnName($column_name),'IS','NOT','NULL');
		else
			return new LMysqlCondition(new LMysqlColumnName($column_name),'!=',new LMysqlValueRenderer($column_value));
	}

	public static function greater_than($column_name,$column_value) {
		ensure_string_not_null("mysql 'greater than' condition",$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'>',new LMysqlValueRenderer($column_value));
	}

	public static function greater_than_or_equal($column_name,$column_value) {
		ensure_string_not_null("mysql 'greater than or equal' condition",$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'>=',new LMysqlValueRenderer($column_value));
	}

	public static function less_than($column_name,$column_value) {
		ensure_string_not_null("mysql 'less than' condition",$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'<',new LMysqlValueRenderer($column_value));
	}

	public static function less_than_or_equal($column_name,$column_value) {
		ensure_string_not_null("mysql 'less than or equal' condition",$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'<=',new LMysqlValueRenderer($column_value));
	}

	public static function rlike($column_name,$pattern) {
		ensure_string_not_null("mysql 'rlike' condition",$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'RLIKE',new LMysqlValueRenderer($pattern));
	}
	
	public static function like($column_name,$pattern,$escape_char=null) {
		ensure_string_not_null("mysql 'like' condition",$column_name);
		if ($escape_char) {
			if (strlen($escape_char)!=1) throw new \Exception("The escape character of like condition can be of only one character : '".$escape_char."' found");
			return new LMysqlCondition(new LMysqlColumnName($column_name),'LIKE',new LMysqlValueRenderer($pattern),'ESCAPE',new LMysqlValueRenderer($escape_char));
		}
		else return new LMysqlCondition(new LMysqlColumnName($column_name),'LIKE',new LMysqlValueRenderer($pattern));
	}
	
	public static function not_like($column_name,$pattern,$escape_char=null) {
		ensure_string_not_null("mysql 'not like' condition",$column_name);
		if ($escape_char) {
			if (strlen($escape_char)!=1) throw new \Exception("The escape character of like condition can be of only one character : '".$escape_char."' found");
			return new LMysqlCondition(new LMysqlColumnName($column_name),'NOT LIKE',new LMysqlValueRenderer($pattern),'ESCAPE',new LMysqlValueRenderer($escape_char));
		}
		else return new LMysqlCondition(new LMysqlColumnName($column_name),'NOT LIKE',new LMysqlValueRenderer($pattern));
	}

	public static function in($column_name,$data_set_or_select) {
		ensure_string_not_null("mysql 'in' condition",$column_name);
		if ($data_set_or_select instanceof LMysqlSelectStatement) return new LMysqlCondition(new LMysqlColumnName($column_name),'IN','(',trim($data_set_or_select),')');
		if ($data_set_or_select==null) $data_set_or_select = ['!'];
		if ($data_set_or_select instanceof LMysqlElementList) return new LMysqlCondition(new LMysqlColumnName($column_name),'IN',$data_set_or_select);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'IN',new LMysqlElementList(... $data_set_or_select));
	}

	public static function not_in($column_name,$data_set_or_select) {
		ensure_string_not_null("mysql 'not in' condition",$column_name);
		if ($data_set_or_select instanceof LMysqlSelectStatement) return new LMysqlCondition(new LMysqlColumnName($column_name),'NOT IN','(',trim($data_set_or_select),')');
		if ($data_set_or_select==null) $data_set_or_select = ['!'];
		if ($data_set_or_select instanceof LMysqlElementList) return new LMysqlCondition(new LMysqlColumnName($column_name),'NOT IN',$data_set_or_select);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'NOT IN',new LMysqlElementList(... $data_set_or_select));
	}

	public static function between($column_name,$start_value,$end_value) {
		ensure_string_not_null("mysql 'between' condition",$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'BETWEEN',$start_value,'AND',$end_value);
	}

	public static function not_between($column_name,$start_value,$end_value) {
		ensure_string_not_null("mysql 'not between' condition",$column_name);
		return new LMysqlCondition(new LMysqlColumnName($column_name),'NOT BETWEEN',$start_value,'AND',$end_value);
	}

	public static function exists($select) {
		ensure_instance_of("mysql 'exists' condition",$select,[LMysqlSelectStatement::class]);
		return new LMysqlCondition('EXISTS(',trim($select),')');
	}

	public static function not_exists($select) {
		ensure_instance_of("mysql 'not exists' condition",$select,[LMysqlSelectStatement::class]);
		return new LMysqlCondition('NOT','EXISTS(',trim($select),')');
	}

	public static function match_against($table_list,$term_list,$boolean_mode=false) {
		$table_names = [];

		if (is_string($table_list)) $table_list = array($table_list);
		foreach ($table_list as $table_name) $table_names[] = new LMysqlTableName($table_name);

		$final_term_list = [];

		if (is_string($term_list)) $term_list = array($term_list);
		foreach ($term_list as $term) {
			$parts = explode("'",$term);
			if (count($parts)==0) continue;
			if (count($parts)==1) $token = $parts[0];
			if (count($parts)==2) {
				$l1 = strlen($parts[0]);
				$l2 = strlen($parts[1]);
				if ($l1>$l2) $token = $parts[0];
				else $token = $parts[1];
			}
			if (count($parts)>2) continue;
			
			$final_term_list []= $token;
		}

		if ($boolean_mode) {
			$term_string = implode(" ",$final_term_list);
			$modifier = " IN BOOLEAN MODE";
		}
		else {
			$term_string = implode(',',$final_term_list);
			$modifier = "";
		}

		return "MATCH(".implode(',',$table_names).") AGAINST('".$term_string."' ".$modifier.")";
	}

}