<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

trait LStandardOperationsFieldsTrait {
	
	public static function hasStandardOperationsColumns() {
		return true;
	}

	public $created_at;
	public $created_by;
	public $last_updated_at;
	public $last_updated_by;
	public $deleted_at;
	public $deleted_by;
}