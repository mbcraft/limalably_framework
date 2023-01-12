<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/


class LMysqlColumnDefinition {
	
	private $column_name;
	private $data_type = null;
	private $not_null = "";
	private $default_value_set = false;
	private $default_value;
	private $generated = "";
	private $auto_increment = false;
	private $column_constraint = "";
	private $position_modifier = "";

	function __construct($column_name) {

		$upper_name = strtoupper($column_name);

		if (in_array($upper_name,LMysqlKeywords::KEYWORD_LIST_UPPERCASE)) 
			throw new \Exception("The column name '".$column_name."' is a reserved mysql keyword and can't be used as column name!");

		$this->column_name = $column_name;
	}

	function generated_stored(string $spec) {
		$this->generated = " GENERATED ALWAYS AS ( ".$spec." ) STORED";

		return $this;
	}

	function generated_virtual(string $spec) {
		$this->generated = " GENERATED ALWAYS AS ( ".$spec." ) VIRTUAL";

		return $this;
	}

	function first() {
		$this->position_modifier = " FIRST";

		return $this;
	}

	function after($column_name) {
		if (!is_string($column_name)) throw new \Exception("After column name is not a valid string!");

		$this->position_modifier = " AFTER ".$column_name;

		return $this;
	}

	function not_null() {
		$this->not_null = " NOT NULL";

		return $this;
	}

	function default_value($value) {
		$v = new LMysqlValueRenderer($value);

		$this->default_value_set = true;
		$this->default_value = " DEFAULT ".$v;

		return $this;
	}

	function auto_increment() {
		$this->auto_increment = true;

		return $this;
	}

	function primary_key() {
		$this->column_constraint  = " PRIMARY KEY";

		return $this;
	}

	function unique_key() {
		$this->column_constraint = " UNIQUE KEY";

		return $this;
	}

	function unique() {
		$this->column_constraint = " UNIQUE";

		return $this;
	}

	function __toString() {
		if ($this->data_type==null) throw new \Exception("Data type is not set in column definition.");

		$result = $this->column_name;
		$result .= " ".$this->data_type;
		if ($this->generated) {
			$result .= $this->generated;
		} else {
			$result.= $this->not_null;
			if ($this->default_value_set) {
				$result .= $this->default_value;
			}
			if ($this->auto_increment) {
				$result .= " AUTO_INCREMENT";
			}
		}
		$result .= $this->column_constraint;
		$result .= $this->position_modifier;

		return $result;
	}

	function t_id() {
		return $this->t_u_bigint()->auto_increment()->not_null()->primary_key();
	}

	function t_external_id() {
		return $this->t_u_bigint();
	}

	function t_boolean() {
		$this->data_type = "BOOLEAN";

		return $this;
	}

	function t_json() {
		$this->data_type = "JSON";

		return $this;
	}

	function t_text16() {
		$this->data_type = "VARCHAR(16)";

		return $this;
	}

	function t_text32() {
		$this->data_type = "VARCHAR(32)";

		return $this;
	}

	function t_text64() {
		$this->data_type = "VARCHAR(64)";

		return $this;
	}

	function t_text128() {
		$this->data_type = "VARCHAR(128)";
		
		return $this;
	}

	function t_text256() {
		$this->data_type = "VARCHAR(256)";

		return $this;
	}

	function t_text512() {
		$this->data_type = "VARCHAR(512)";

		return $this;
	}

	function t_text1024() {
		$this->data_type = "VARCHAR(1024)";

		return $this;
	}

	function t_text2048() {
		$this->data_type = "VARCHAR(2048)";

		return $this;
	}

	function t_geometry() {
		$this->data_type = "GEOMETRY";

		return $this;
	}

	function t_point() {
		$this->data_type = "POINT";

		return $this;
	}

	function t_linestring() {
		$this->data_type = "LINESTRING";

		return $this;
	}

	function t_polygon() {
		$this->data_type = "POLYGON";

		return $this;
	}

	function t_geometrycollection() {
		$this->data_type = "GEOMETRYCOLLECTION";

		return $this;
	}

	function t_multilinestring() {
		$this->data_type = "MULTILINESTRING";

		return $this;
	}

	function t_multipoint() {
		$this->data_type = "MULTIPOINT";

		return $this;
	}

	function t_multipolygon() {
		$this->data_type = "MULTIPOLYGON";

		return $this;
	}

	function t_year() {
		$this->data_type = "YEAR";

		return $this;
	}

	function t_timestamp() {
		$this->data_type = "TIMESTAMP";

		return $this;
	}

	function t_datetime() {
		$this->data_type = "DATETIME";

		return $this;
	}

	function t_time() {
		$this->data_type = "TIME";

		return $this;
	}

	function t_date() {
		$this->data_type = "DATE";

		return $this;
	}

	function t_blob() {
		$this->data_type = "BLOB";

		return $this;
	}

	function t_longblob() {
		$this->data_type = "LONGBLOB";

		return $this;
	}

	function t_text() {
		$this->data_type = "TEXT";

		return $this;
	}

	function t_longtext() {
		$this->data_type = "LONGTEXT";

		return $this;
	}

	function t_enum(array $values) {

		$el = new LMysqlElementList($values);

		$this->data_type = "ENUM(".$el->toEscapedStringList().")";

		return $this;
	}

	function t_set(array $values) {

		$el = new LMysqlElementList($values);

		$this->data_type = "SET(".$el->toEscapedStringList().")";

		return $this;
	}

	private function t_tinyint($signed=false) {
		$this->data_type = "TINYINT ".($signed ? "SIGNED" : "UNSIGNED");

		return $this;
	}

	function t_s_tinyint() {
		return $this->t_tinyint(true);
	}

	function t_u_tinyint() {
		return $this->t_tinyint(false);
	}

	private function t_int($signed=false) {
		$this->data_type = "INT ".($signed ? "SIGNED" : "UNSIGNED");

		return $this;	
	}

	function t_u_int() {
		return $this->t_int(false);
	}

	function t_s_int() {
		return $this->t_int(true);
	}

	private function t_bigint($signed=false) {
		$this->data_type = "BIGINT ".($signed ? "SIGNED" : "UNSIGNED");

		return $this;	
	}

	function t_u_bigint() {
		return $this->t_bigint(false);
	}

	function t_s_bigint() {
		return $this->t_bigint(true);
	}

	function t_float() {
		$this->data_type = "FLOAT";

		return $this;
	}

	function t_bit(int $size) {
		$this->data_type = "BIT(".$size.")";
	}
}