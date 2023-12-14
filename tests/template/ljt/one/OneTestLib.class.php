<?php


class OneTestLib extends LJAbstractTemplate {
	
	const SIMPLE_FIELDS = ["text"];

	const TEMPLATE_FIELDS = ["content"];

	public function render() {
		
		if ($this->has('text')) { ?>
		<h1><?= $this->text ?></h1>
		<?php } ?>
		<div class="container">
			<div class="row">
			<?= $this->content ?>
			</div>
		</div>
		<?php
	}

}