<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlWhereBlock extends LMysqlAbstractConditionsBlock {
	
	public function __construct($element) {
		parent::__construct('where',$element);
	}
}