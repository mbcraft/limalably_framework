<?php



interface LIMigration {
	
	public function execute();

	public function revert();

}