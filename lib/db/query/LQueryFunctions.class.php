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

		function insert(string $table_name,$column_list=null,$data=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlInsertStatement($table_name,$column_list,$data);
		}

		function update(string $table_name,$name_value_pair_list,$where_block=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlUpdateStatement($table_name,$name_value_pair_list,$where_block);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function delete(string $table_name,$where_block=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlDeleteStatement($table_name,$where_block);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function replace(string $table_name,$column_list,$select_set_or_values) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlReplaceStatement($table_name,$column_list,$select_set_or_values);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function truncate(string $table_name) {
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

		function _nl(string $field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _is_null(string $field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_nl(string $field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_not_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}		

		function _is_not_null(string $field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::is_not_null($field_name);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _eq(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function _equal(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_eq(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function _not_equal(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _gt(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}


		function _greater_than(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _gt_eq(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _greater_than_or_equal(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::greater_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _lt(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _less_than(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _lt_eq(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _less_than_or_equal(string $field_name,$field_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::less_than_or_equal($field_name,$field_value);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _rlike(string $field_name,$pattern) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::rlike($field_name,$pattern);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _like(string $field_name,$pattern,$escape_char=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::like($field_name,$pattern,$escape_char);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_like(string $field_name,$pattern,$escape_char=null) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_like($field_name,$pattern,$escape_char);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _in(string $field_name,$data) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::in($field_name,$data);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_in(string $field_name,$data) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_in($field_name,$data);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _bt(string $field_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _between(string $field_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _n_bt(string $field_name,$start,$end) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return LMysqlCondition::not_between($field_name,$start,$end);

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function _not_between(string $field_name,$start,$end) {
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

		function __replace_value(string $field_name,$search_value,$replace_value) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlReplaceValue($field_name,$search_value,$replace_value);
			
			LQueryFunctions::throwQueryLayerNotFound();
		}

		function __repl(string $field_name,$search_value,$replace_value) {
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

		function asc(string $field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return $field_name." ASC";

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function desc(string $field_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return $field_name." DESC";

			LQueryFunctions::throwQueryLayerNotFound();
		}

		function f(string $field_name) {
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

		function table_description(string $table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlTableDescriptionStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function create_table(string $table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlCreateTableStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function drop_table(string $table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlDropTableStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function rename_table(string $old_table_name,string $new_table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlRenameTableStatement($old_table_name,$new_table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function alter_table(string $table_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlAlterTableStatement($table_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function col_def(string $column_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlColumnDefinition($column_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function foreign_key_checks(bool $enable) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlForeignKeyChecksStatement($enable);

			LQueryFunctions::throwQueryLayerNotFound();	
		}

		function fk_def(string $constraint_name) {
			LQueryFunctions::checkLayerSelected();

			if (LQueryFunctions::usingMysqlLayer()) return new LMysqlForeignKeyConstraintDefinition($constraint_name);

			LQueryFunctions::throwQueryLayerNotFound();	
		}



	}



}