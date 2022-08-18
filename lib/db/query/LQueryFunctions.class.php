<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LQueryFunctions {
		
	private static $initialized = false;
	private static $current_layer_name = null;

	public static function useMysqlLayer() {
		self::init();

		self::$current_layer_name = 'mysql';
	}

	private static function checkLayerSelected() {
		if (self::$current_layer_name==null) throw new \Exception("Query function layer is not selected correctly.");
	}

	private static function throwFunctionNotSupported($function_name) {
		throw new \Exception("In function query layer ".self::$current_layer_name." the function ".$function_name." is not supported!");
	}

	public static function usingMysqlLayer() {

		return self::$current_layer_name == 'mysql';
	}

	private static function throwQueryLayerNotFound() {
		throw new \Exception("Query function layer not found : ".self::$current_layer_name);
	}

	public static function init() {
		if (self::$initialized) return;

		self::$initialized = true;

		function select($field_name_list,$table_name_list,$where_block=null) {
			 LQueryFunctions::checkLayerSelected();

			 if (LQueryFunctions::usingMysqlLayer()) return new LMysqlSelectStatement($field_name_list,$table_name_list,$where_block);

			 LQueryFunctions::throwQueryLayerNotFound();
		}

		function insert($table_name,$column_list,$data) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlInsertStatement($table_name,$column_list,$data);
		}

		function update($table_name,$name_value_pair_list,$where_block=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlUpdateStatement($table_name,$name_value_pair_list,$where_block);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function delete($table_name,$where_block) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlDeleteStatement($table_name,$where_block=null);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function replace($table_name,$column_list,$select_set_or_values) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlReplaceStatement($table_name,$column_list,$select_set_or_values);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function truncate($table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlTruncateStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _and(... $elements) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlAndBlock(... $elements);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _or(... $elements) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlOrBlock(... $elements);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function nl($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function isnull($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function n_nl($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_not_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}		

		function is_not_null($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_not_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function eq($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function equal($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function n_eq($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function not_equal($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function gt($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function greater_than($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function gt_eq($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function greater_than_or_equal($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function lt($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function less_than($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function lt_eq($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function less_than_or_equal($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function rlike($field_name,$pattern) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::rlike($field_name,$pattern);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function like($field_name,$pattern,$escape_char=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::like($field_name,$pattern,$escape_char);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function not_like($field_name,$pattern,$escape_char=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_like($field_name,$pattern,$escape_char);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function in($field_name,$data) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::in($field_name,$data);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function not_in($field_name,$data) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_in($field_name,$data);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function bt($field_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function between($field_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function n_bt($field_name,$start_end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function not_between($field_name,$start_end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function ex($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}		

		function exists($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function n_ex($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function not_exists($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function wh($something) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlWhereBlock($something);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function where($something) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlWhereBlock($something);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function on($something) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlOnBlock($something);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function el(... $elements) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlElementList(... $elements);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function ell(... $lists) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlElementListList(... $lists);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function asc($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return $field_name." ASC";

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function desc($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return $field_name." DESC";

			LQueryFunctions::throwQueryLayerNotFound();
		}

	}



}