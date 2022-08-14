<?php


class LMysqlQueryFunctionLayer {
	
	private static $initialized = false;


	public static function init() {
		if (self::$initialized) return;

		self::$initialized = true;

		function select($field_name_list,$table_name_list,$where_block) {
			return new LMysqlSelectStatement($field_name_list,$table_name_list,$where_block);
		}

		function insert($table_name,$column_list,$data) {
			return new LMysqlInsertStatement($table_name,$column_list,$data);
		}

		function update($table_name,$name_value_pair_list,$where_block) {
			return new LMysqlUpdateStatement($table_name,$name_value_pair_list,$where_block);
		}

		function delete($table_name,$where_block) {
			return new LMysqlDeleteStatement($table_name,$where_block);
		}

		function replace($table_name,$column_list,$select_set_or_values) {
			return new LMysqlReplaceStatement($table_name,$column_list,$select_set_or_values);
		}

		function truncate($table_name) {
			return new LMysqlTruncateStatement($table_name);
		}

		function and(... $elements) {
			return new LMysqlAndBlock(... $elements);
		}

		function or(... $elements) {
			return new LMysqlOrBlock(... $elements);
		}

		function nl($field_name) {
			return LMysqlCondition::is_null($field_name);
		}

		function is_null($field_name) {
			return LMysqlCondition::is_null($field_name);
		}

		function n_nl($field_name) {
			return LMysqlCondition::is_not_null($field_name);
		}		

		function is_not_null($field_name) {
			return LMysqlCondition::is_not_null($field_name);
		}

		function eq($field_name,$field_value) {
			return LMysqlCondition::equal($field_name,$field_value);
		}


		function equal($field_name,$field_value) {
			return LMysqlCondition::equal($field_name,$field_value);
		}

		function n_eq($field_name,$field_value) {
			return LMysqlCondition::not_equal($field_name,$field_value);
		}


		function not_equal($field_name,$field_value) {
			return LMysqlCondition::not_equal($field_name,$field_value);
		}

		function gt($field_name,$field_value) {
			return LMysqlCondition::greater_than($field_name,$field_value);
		}


		function greater_than($field_name,$field_value) {
			return LMysqlCondition::greater_than($field_name,$field_value);
		}

		function gt_eq($field_name,$field_value) {
			return LMysqlCondition::greater_than_or_equal($field_name,$field_value);
		}

		function greater_than_or_equal($field_name,$field_value) {
			return LMysqlCondition::greater_than_or_equal($field_name,$field_value);
		}

		function lt($field_name,$field_value) {
			return LMysqlCondition::less_than($field_name,$field_value);
		}

		function less_than($field_name,$field_value) {
			return LMysqlCondition::less_than($field_name,$field_value);
		}

		function lt_eq($field_name,$field_value) {
			return LMysqlCondition::less_than_or_equal($field_name,$field_value);
		}

		function less_than_or_equal($field_name,$field_value) {
			return LMysqlCondition::less_than_or_equal($field_name,$field_value);
		}

		function rlike($field_name,$pattern) {
			return LMysqlCondition::rlike($field_name,$pattern);
		}

		function like($field_name,$pattern,$escape_char=null) {
			return LMysqlCondition::like($field_name,$pattern,$escape_char);
		}

		function not_like($field_name,$pattern,$escape_char=null) {
			return LMysqlCondition::not_like($field_name,$pattern,$escape_char);
		}

		function in($field_name,$data) {
			return LMysqlCondition::in($field_name,$data);
		}

		function not_in($field_name,$data) {
			return LMysqlCondition::not_in($field_name,$data);
		}

		function bt($field_name,$start,$end) {
			return LMysqlCondition::between($field_name,$start,$end);
		}

		function between($field_name,$start,$end) {
			return LMysqlCondition::between($field_name,$start,$end);
		}

		function n_bt($field_name,$start_end) {
			return LMysqlCondition::not_between($field_name,$start,$end);
		}

		function not_between($field_name,$start_end) {
			return LMysqlCondition::not_between($field_name,$start,$end);
		}

		function ex($select) {
			return LMysqlCondition::exists($select);
		}		

		function exists($select) {
			return LMysqlCondition::exists($select);
		}

		function n_ex($select) {
			return LMysqlCondition::not_exists($select);
		}

		function not_exists($select) {
			return LMysqlCondition::not_exists($select);
		}

		function wh($something) {
			return new LMysqlWhereBlock($something);
		}

		function where($something) {
			return new LMysqlWhereBlock($something);
		}

		function on($something) {
			return new LMysqlOnBlock($something);
		}

		function el(... $elements) {
			return new LMysqlElementList(... $elements);
		}

		function ell(... $lists) {
			return new LMysqlElementListList(... $lists);
		}

		function asc($field_name) {
			return $field_name." ASC";
		}

		function desc($field_name) {
			return $field_name." DESC";
		}

	}


}