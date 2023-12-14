<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class THelloWorld extends LJAbstractTemplatePart {
	

	function render() {

		$t = tag('h2');
		$t[] = 'Hello world!';

		return $t;

	}

}