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
		return new LMysqlCondition(new LMysqlColumnName($column_name),'=',new LMysqlValueRenderer($column_value));
	}

	public static function not_equal($column_name,$column_value) {
		ensure_string_not_null("mysql 'not equal' condition",$column_name);
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

}