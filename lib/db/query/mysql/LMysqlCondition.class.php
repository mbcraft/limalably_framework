<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlCondition {

	private $parts;

	private function __construct(... $parts) {
		$this->parts = $parts;
	}

	public function __toString() {
		return implode(' ',$this->parts);
	}

	private static function prepare_value($val) {
		if ($val===null) return 'NULL';
		if (is_string($val)) return "'".mysqli_real_escape_string(LDbConnectionManager::getLastConnectionUsed()->getHandle(),$val)."'";
		else return $val;
	}

	public static function is_null($field_name) {
		ensure_string_not_null("mysql 'is null' condition",$field_name);
		return new LMysqlCondition($field_name,'IS','NULL');
	}

	public static function is_not_null($field_name) {
		ensure_string_not_null("mysql 'is not null' condition",$field_name);
		return new LMysqlCondition($field_name,'IS','NOT','NULL');
	}

	public static function equal($field_name,$field_value) {
		ensure_string_not_null("mysql 'equal' condition",$field_name);
		return new LMysqlCondition($field_name,'=',self::prepare_value($field_value));
	}

	public static function not_equal($field_name,$field_value) {
		ensure_string_not_null("mysql 'not equal' condition",$field_name);
		return new LMysqlCondition($field_name,'!=',self::prepare_value($field_value));
	}

	public static function greater_than($field_name,$field_value) {
		ensure_string_not_null("mysql 'greater than' condition",$field_name);
		return new LMysqlCondition($field_name,'>',self::prepare_value($field_value));
	}

	public static function greater_than_or_equal($field_name,$field_value) {
		ensure_string_not_null("mysql 'greater than or equal' condition",$field_name);
		return new LMysqlCondition($field_name,'>=',self::prepare_value($field_value));
	}

	public static function less_than($field_name,$field_value) {
		ensure_string_not_null("mysql 'less than' condition",$field_name);
		return new LMysqlCondition($field_name,'<',self::prepare_value($field_value));
	}

	public static function less_than_or_equal($field_name,$field_value) {
		ensure_string_not_null("mysql 'less than or equal' condition",$field_name);
		return new LMysqlCondition($field_name,'<=',self::prepare_value($field_value));
	}

	public static function rlike($field_name,$pattern) {
		ensure_string_not_null("mysql 'rlike' condition",$field_name);
		return new LMysqlCondition($field_name,'RLIKE',self::prepare_value($pattern));
	}
	
	public static function like($field_name,$pattern,$escape_char=null) {
		ensure_string_not_null("mysql 'like' condition",$field_name);
		if ($escape_char) {
			if (strlen($escape_char)!=1) throw new \Exception("The escape character of like condition can be of only one character : '".$escape_char."' found");
			return new LMysqlCondition($field_name,'LIKE',self::prepare_value($pattern),'ESCAPE',self::prepare_value($escape_char));
		}
		else return new LMysqlCondition($field_name,'LIKE',self::prepare_value($pattern));
	}
	
	public static function not_like($field_name,$pattern,$escape_char=null) {
		ensure_string_not_null("mysql 'not like' condition",$field_name);
		if ($escape_char) {
			if (strlen($escape_char)!=1) throw new \Exception("The escape character of like condition can be of only one character : '".$escape_char."' found");
			return new LMysqlCondition($field_name,'NOT LIKE',self::prepare_value($pattern),'ESCAPE',self::prepare_value($escape_char));
		}
		else return new LMysqlCondition($field_name,'NOT LIKE',self::prepare_value($pattern));
	}

	public static function in($field_name,$data_set_or_select) {
		ensure_string_not_null("mysql 'in' condition",$field_name);
		if ($data_set_or_select instanceof LMysqlSelectStatement) return new LMysqlCondition($field_name,'IN','(',trim($data_set_or_select),')');
		if ($data_set_or_select==null) $data_set_or_select = ['!'];
		return new LMysqlCondition($field_name,'IN',new LMysqlElementList(... $data_set_or_select));
	}

	public static function not_in($field_name,$data_set_or_select) {
		ensure_string_not_null("mysql 'not in' condition",$field_name);
		if ($data_set_or_select instanceof LMysqlSelectStatement) return new LMysqlCondition($field_name,'NOT IN','(',trim($data_set_or_select),')');
		if ($data_set_or_select==null) $data_set_or_select = ['!'];
		return new LMysqlCondition($field_name,'NOT IN',new LMysqlElementList(... $data_set_or_select));
	}

	public static function between($field_name,$start_value,$end_value) {
		ensure_string_not_null("mysql 'between' condition",$field_name);
		return new LMysqlCondition($field_name,'BETWEEN',$start_value,'AND',$end_value);
	}

	public static function not_between($field_name,$start_value,$end_value) {
		ensure_string_not_null("mysql 'not between' condition",$field_name);
		return new LMysqlCondition($field_name,'NOT BETWEEN',$start_value,'AND',$end_value);
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