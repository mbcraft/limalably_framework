<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class PhpTemplateTest extends LTestCase {
	

	function testTemplateRendering() {


		$t_source = <<<EOT
<?php

?>
Hello world!! : <?= \$greet ?>

EOT;

		$t = new LPhpTemplate($t_source);

		$data = array('greet' => 'Ciao!');

		$result = $t->render($data);

		$this->assertEqual($result,"Hello world!! : Ciao!","Il risultato del rendering non corrisponde!!");

		$cache_dir = $_SERVER['FRAMEWORK_DIR'].'lib/template/drivers/php/.cache/';

		$d = new LDir($cache_dir);
		$d->delete(true);
		
	}

}