<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


interface LIIndexDescription {
	

	function isUnique();

	function isForeignKey();

	function getTable();

	function getColumnName();

	function isNullAllowed();

	function getConstraintName();


}