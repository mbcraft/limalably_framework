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

	public static function checkLayerSelected() {
		if (self::$current_layer_name==null) throw new \Exception("Query function layer is not selected correctly.");
	}

	public static function throwFunctionNotSupported($function_name) {
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

		function delete($table_name,$where_block=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlDeleteStatement($table_name,$where_block);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function replace($table_name,$column_list,$select_set_or_values) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlReplaceStatement($table_name,$column_list,$select_set_or_values);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function truncate($table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlTruncateTableStatement($table_name);

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

		function _nl($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _is_null($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_nl($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_not_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}		

		function _is_not_null($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_not_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _eq($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function _equal($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_eq($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function _not_equal($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _gt($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function _greater_than($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _gt_eq($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _greater_than_or_equal($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _lt($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _less_than($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _lt_eq($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _less_than_or_equal($field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _rlike($field_name,$pattern) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::rlike($field_name,$pattern);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _like($field_name,$pattern,$escape_char=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::like($field_name,$pattern,$escape_char);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_like($field_name,$pattern,$escape_char=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_like($field_name,$pattern,$escape_char);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _in($field_name,$data) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::in($field_name,$data);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_in($field_name,$data) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_in($field_name,$data);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _bt($field_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _between($field_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_bt($field_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_between($field_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _ex($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}		

		function _exists($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_ex($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_exists($select) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_exists($select);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function __replace_value($field_name,$search_value,$replace_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlReplaceValue($field_name,$search_value,$replace_value);
			
			LQueryFunctions::throwQueryLayerNotFound();
		}

		function __repl($field_name,$search_value,$replace_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlReplaceValue($field_name,$search_value,$replace_value);

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

		function f($field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlTableField($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function p() {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlValuePlaceholder();

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function table_list() {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlTableListStatement();

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function table_description($table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlTableDescriptionStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

	}



}