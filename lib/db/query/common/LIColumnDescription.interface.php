<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

interface LIColumnDescription {
	
	public function getColumnName();

	public function getColumnType();

	public function isNull();

	public function key();

	public function getDefaultValue();

	public function getExtraInfo();
}