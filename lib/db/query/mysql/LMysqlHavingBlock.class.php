<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlHavingBlock extends LMysqlAbstractConditionsBlock {
	
	public function __construct($element) {
		parent('having',$element);
	}


}