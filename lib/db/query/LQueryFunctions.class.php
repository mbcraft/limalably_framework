<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LQueryFunctions {
		
	private static $initialized = false;
	private static $current_layer_name = null;

	private static function useMysqlLayer() {
		self::init();

		self::$current_layer_name = 'mysql';
	}

	private static function checkLayerSelected() {
		if (self::$current_layer_name==null) throw new \Exception("Query function layer is not selected correctly.");
	}

	private static function throwFunctionNotSupported($function_name) {
		throw new \Exception("In function query layer ".self::$current_layer_name." the function ".$function_name." is not supported!");
	}

	private static function usingMysqlLayer() {

		return self::$current_layer_name == 'mysql';
	}

	private static function throwQueryLayerNotFound() {
		throw new \Exception("Query function layer not found : ".self::$current_layer_name);
	}

	public static function init() {
		if (self::$initialized) return;

		self::$initialized = true;

		function select($field_name_list,$table_name_list,$where_block=null) {
			 self::checkLayerSelected();

			 if (self::usingMysqlLayer()) return new LMysqlSelectStatement($field_name_list,$table_name_list,$where_block);

			 self::throwQueryLayerNotFound();
		}

		function insert($table_name,$column_list,$data) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlInsertStatement($table_name,$column_list,$data);
		}

		function update($table_name,$name_value_pair_list,$where_block=null) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlUpdateStatement($table_name,$name_value_pair_list,$where_block);

			self::throwQueryLayerNotFound();
		}

		function delete($table_name,$where_block) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlDeleteStatement($table_name,$where_block=null);

			self::throwQueryLayerNotFound();
		}

		function replace($table_name,$column_list,$select_set_or_values) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlReplaceStatement($table_name,$column_list,$select_set_or_values);

			self::throwQueryLayerNotFound();
		}

		function truncate($table_name) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlTruncateStatement($table_name);

			self::throwQueryLayerNotFound();
		}

		function and(... $elements) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlAndBlock(... $elements);

			self::throwQueryLayerNotFound();
		}

		function or(... $elements) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlOrBlock(... $elements);

			self::throwQueryLayerNotFound();
		}

		function nl($field_name) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::is_null($field_name);

			self::throwQueryLayerNotFound();
		}

		function is_null($field_name) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::is_null($field_name);

			self::throwQueryLayerNotFound();
		}

		function n_nl($field_name) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::is_not_null($field_name);

			self::throwQueryLayerNotFound();
		}		

		function is_not_null($field_name) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::is_not_null($field_name);

			self::throwQueryLayerNotFound();
		}

		function eq($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::equal($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}


		function equal($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::equal($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}

		function n_eq($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::not_equal($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}


		function not_equal($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::not_equal($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}

		function gt($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::greater_than($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}


		function greater_than($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::greater_than($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}

		function gt_eq($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::greater_than_or_equal($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}

		function greater_than_or_equal($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::greater_than_or_equal($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}

		function lt($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::less_than($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}

		function less_than($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::less_than($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}

		function lt_eq($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::less_than_or_equal($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}

		function less_than_or_equal($field_name,$field_value) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::less_than_or_equal($field_name,$field_value);

			self::throwQueryLayerNotFound();
		}

		function rlike($field_name,$pattern) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::rlike($field_name,$pattern);

			self::throwQueryLayerNotFound();
		}

		function like($field_name,$pattern,$escape_char=null) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::like($field_name,$pattern,$escape_char);

			self::throwQueryLayerNotFound();
		}

		function not_like($field_name,$pattern,$escape_char=null) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::not_like($field_name,$pattern,$escape_char);

			self::throwQueryLayerNotFound();
		}

		function in($field_name,$data) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::in($field_name,$data);

			self::throwQueryLayerNotFound();
		}

		function not_in($field_name,$data) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::not_in($field_name,$data);

			self::throwQueryLayerNotFound();
		}

		function bt($field_name,$start,$end) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::between($field_name,$start,$end);

			self::throwQueryLayerNotFound();
		}

		function between($field_name,$start,$end) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::between($field_name,$start,$end);

			self::throwQueryLayerNotFound();
		}

		function n_bt($field_name,$start_end) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::not_between($field_name,$start,$end);

			self::throwQueryLayerNotFound();
		}

		function not_between($field_name,$start_end) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::not_between($field_name,$start,$end);

			self::throwQueryLayerNotFound();
		}

		function ex($select) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::exists($select);

			self::throwQueryLayerNotFound();
		}		

		function exists($select) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::exists($select);

			self::throwQueryLayerNotFound();
		}

		function n_ex($select) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::not_exists($select);

			self::throwQueryLayerNotFound();
		}

		function not_exists($select) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return LMysqlCondition::not_exists($select);

			self::throwQueryLayerNotFound();
		}

		function wh($something) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlWhereBlock($something);

			self::throwQueryLayerNotFound();
		}

		function where($something) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlWhereBlock($something);

			self::throwQueryLayerNotFound();
		}

		function on($something) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlOnBlock($something);

			self::throwQueryLayerNotFound();
		}

		function el(... $elements) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlElementList(... $elements);

			self::throwQueryLayerNotFound();
		}

		function ell(... $lists) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return new LMysqlElementListList(... $lists);

			self::throwQueryLayerNotFound();
		}

		function asc($field_name) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return $field_name." ASC";

			self::throwQueryLayerNotFound();
		}

		function desc($field_name) {
			self::checkLayerSelected();

			if (self::usingMysqlLayer()) return $field_name." DESC";

			self::throwQueryLayerNotFound();
		}

	}



}