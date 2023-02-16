<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlIndexDescription implements LIIndexDescription {
	
	private $table;
	private $non_unique;
	private $key_name;
	private $seq_in_index;
	private $column_name;
	private $collation;
	private $cardinality;
	private $sub_part;
	private $packed;
	private $null_allowed;
	private $index_type;
	private $comment;
	private $index_comment;
	private $visible;
	private $expression;


	function __construct($Table,$Non_unique,$Key_name,$Seq_in_index,$Column_name,$Collation,$Cardinality,$Sub_part,$Packed,$Null,$Index_type,$Comment,$Index_comment,$Visible,$Expression) {

		$this->table = $Table;
		$this->non_unique = $Non_unique;
		$this->key_name = $Key_name;
		$this->seq_in_index = $Seq_in_index;
		$this->column_name = $Column_name;
		$this->collation = $Collation;
		$this->cardinality = $Cardinality;
		$this->sub_part = $Sub_part;
		$this->packed = $Packed;
		$this->null_allowed = $Null;
		$this->index_type = $Index_type;
		$this->comment = $Comment;
		$this->visible = $Visible;
		$this->expression = $Expression;

	}

	public function isUnique() {
		return !$this->non_unique;
	}

	public function isForeignKey() {
		return $this->non_unique;
	}

	public function getTable() {
		return $this->table;
	}

	public function getColumnName() {
		return $this->column_name;
	}

	public function isNullAllowed() {
		return $this->null_allowed == 'YES';
	}

	public function getConstraintName() {
		return $this->key_name;
	}

}