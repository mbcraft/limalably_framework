<?php


abstract class LMysqlAbstractConditionsBlock {
	
	private $mode = null;
	private $element;

	public function __construct($mode,$element) {
		if ($mode!='where' && $mode!='having' && $mode!='on') throw new \Exception("mode of conditions block is neither 'where' nor 'having'.");
		$this->mode = $mode;

		ensure_instance_of($mode." conditions block of mysql statement",$element,[LMysqlCondition::class,LMysqlOrBlock::class,LMysqlAndBlock::class]);

		$this->element = $element;

	}

	public function __toString() {
		return " ".strtoupper($this->mode)." ".$this->element;
	}

}