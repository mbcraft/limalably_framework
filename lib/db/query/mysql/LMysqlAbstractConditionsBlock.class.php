<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*

Thanks to www.mysqltutorial.org for its documentation.

*/


abstract class LMysqlAbstractConditionsBlock {
	
	private $mode = null;
	private $element;

	public function __construct($mode,$element) {
		if ($mode!='where' && $mode!='having' && $mode!=' on') throw new \Exception("mode of conditions block is neither 'where' nor 'having'.");
		$this->mode = $mode;

		if (is_array($element)) {
			if (count($element)>0) 
				$element = new LMysqlAndBlock(... $element);
			else 
				$element = $element[0];
		}
		if ($element instanceof LMysqlElementList) $element = new LMysqlAndBlock(... $element->getElements());

		ensure_instance_of($mode." conditions block of mysql statement",$element,[LMysqlCondition::class,LMysqlOrBlock::class,LMysqlAndBlock::class]);

		$this->element = $element;

	}

	public function __toString() {
		return strtoupper($this->mode)." ".$this->element;
	}

}