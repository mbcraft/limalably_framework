<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class OneAnotherTestLib extends LJAbstractTemplate {
	
	const SIMPLE_FIELDS = ["text"];

	function render() {
	?>
	<h2><?= $this->text ?></h2>
	<?php
	}

}