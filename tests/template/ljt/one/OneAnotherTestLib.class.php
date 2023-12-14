<?php


class OneAnotherTestLib extends LJAbstractTemplate {
	
	const SIMPLE_FIELDS = ["text"];

	function render() {
	?>
	<h2><?= $this->text ?></h2>
	<?php
	}

}