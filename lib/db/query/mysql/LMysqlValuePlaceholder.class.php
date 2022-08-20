<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlValuePlaceholder {
	
	function __construct() {

	}

	function __toString() {
		return '?';
	}


}