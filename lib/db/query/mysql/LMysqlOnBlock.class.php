<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LMysqlOnBlock extends LMysqlAbstractConditionsBlock {
	

	public function __construct($element) {
		parent::__construct(' on',$element);
	}

}