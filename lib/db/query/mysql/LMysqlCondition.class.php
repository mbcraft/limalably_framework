<?php


class LMysqlCondition {

	private $parts;

	private function __construct(... $parts) {
		$this->parts = $parts;
	}

	public function __toString() {
		return implode(' ',$this->parts);
	}

	private function ensure_field_name_string_not_null($field_name) {
		if ($field_name==null) throw new \Exception("Field name is null in mysql condition : found ".$field_name);
		if (!is_string($field_name)) throw new \Exception("Field name is not a string in mysql condition : found ".$field_name);
	}

	private function prepare_value($val) {
		if ($val===null) return 'NULL';
		if (is_string($val)) return "'".mysqli_real_escape_string($val)."'";
		else return $val;
	}

	public static function is_null($field_name) {
		$this->ensure_field_name_string_not_null($field_name);
		return new LMysqlCondition($field_name,'IS','NULL');
	}

	public static function is_not_null($field_name) {
		$this->ensure_field_name_string_not_null($field_name);
		return new LMysqlCondition($field_name,'IS','NOT','NULL');
	}

	public static function equals($field_name,$field_value) {
		$this->ensure_field_name_string_not_null($field_name);
		return new LMysqlCondition($field_name,'=',$this->prepare_value($field_value));
	}

	public static function not_equals($field_name,$field_value) {
		$this->ensure_field_name_string_not_null($field_name);
		return new LMysqlCondition($field_name,'!=',$this->prepare_value($field_value));
	}

	public static function greater_than($field_name,$field_value) {
		$this->ensure_field_name_string_not_null($field_name);
		return new LMysqlCondition($field_name,'>',$this->prepare_value($field_value));
	}

	public static function greater_than_or_equal($field_name,$field_value) {
		$this->ensure_field_name_string_not_null($field_name);
		return new LMysqlCondition($field_name,'>=',$this->prepare_value($field_value));
	}

	public static function less_than($field_name,$field_value) {
		$this->ensure_field_name_string_not_null($field_name);
		return new LMysqlCondition($field_name,'<',$this->prepare_value($field_value));
	}

	public static function less_than_or_equal($field_name,$field_value) {
		$this->ensure_field_name_string_not_null($field_name);
		return new LMysqlCondition($field_name,'<=',$this->prepare_value($field_value));
	}
	
	public static function like($field_name,$pattern,$escape_char=null) {
		$this->ensure_field_name_string_not_null($field_name);
		if ($escape_char) {
			if (strlen($escape_char)!=1) throw new \Exception("The escape character of like condition can be of only one character : '".$escape_char."' found");
			return new LMysqlCondition($field_name,'LIKE',$this->prepare_value($field_value),'ESCAPE',$this->prepare_value($escape_char));
		}
		else return new LMysqlCondition($field_name,'LIKE',$this->prepare_value($field_value));
	}
	
	public static function not_like($field_name,$pattern,$escape_char=null) {
		$this->ensure_field_name_string_not_null($field_name);
		if ($escape_char) {
			if (strlen($escape_char)!=1) throw new \Exception("The escape character of like condition can be of only one character : '".$escape_char."' found");
			return new LMysqlCondition($field_name,'NOT LIKE',$this->prepare_value($field_value),'ESCAPE',$this->prepare_value($escape_char));
		}
		else return new LMysqlCondition($field_name,'NOT_LIKE',$this->prepare_value($field_value));
	}

	public static function in($field_name,$data_set_or_select) {
		$this->ensure_field_name_string_not_null($field_name);
		if ($data_set_or_select instanceof LMysqlSelectStatement) return LMysqlCondition($field_name,'IN','(',$data_set_or_select,')');
		if ($data_set_or_select==null) $data_set_or_select = ['!'];
		return LMysqlCondition($field_name,'IN',new LMysqlElementList($data_set_or_select));
	}

	public static function not_in($field_name,$data_set_or_select) {
		$this->ensure_field_name_string_not_null($field_name);
		if ($data_set_or_select instanceof LMysqlSelectStatement) return LMysqlCondition($field_name,'NOT IN','(',$data_set_or_select,')');
		if ($data_set_or_select==null) $data_set_or_select = ['!'];
		return LMysqlCondition($field_name,'NOT IN',new LMysqlElementList($data_set_or_select));
	}

	public static function between($field_name,$start_value,$end_value) {
		$this->ensure_field_name_string_not_null($field_name);
		return new LMysqlCondition($field_name,'BETWEEN',$start_value,'AND',$end_value);
	}

	public static function not_between($field_name,$start_value,$end_value) {
		$this->ensure_field_name_string_not_null($field_name);
		return new LMysqlCondition($field_name,'NOT BETWEEN',$start_value,'AND',$end_value);
	}

	public static function exists($select) {
		ensure_instance_of("exists condition of mysql query",$select,[LMysqlSelectStatement::class]);
		return new LMysqlCondition('EXISTS','(',$select,')');
	}

	public static function not_exists($select) {
		ensure_instance_of("not exists condition of mysql query",$select,[LMysqlSelectStatement::class]);
		return new LMysqlCondition('NOT','EXISTS','(',$select,')');
	}

}