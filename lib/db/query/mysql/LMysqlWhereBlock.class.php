<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/

class LMysqlWhereBlock extends LMysqlAbstractConditionsBlock {
	
	public function __construct($element) {
		parent::__construct('where',$element);
	}
}