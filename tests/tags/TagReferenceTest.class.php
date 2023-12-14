<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class TagReferenceTest extends LTestCase {
	
	function testFound() {


		$p = new LTag('abcd');
		$p->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p->setIndentMode(LTag::TAG_INDENT_SKIP_ALL);
		$p["prova"] = "Ciao";

		$p[] = new LTagReference('prova');

		try {
			$p_res = "".$p;
		} catch (\Exception $ex) {
			$this->fail("Unable to render parent tag prova that should be found");
		}

	}

	function testNotFound() {


		$p = new LTag('abcd');
		$p->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p->setIndentMode(LTag::TAG_INDENT_SKIP_ALL);
		

		$p[] = new LTagReference('prova');

		try {
			$p_res = "".$p;
			$this->fail("Render parent tag prova that should not be found");
		} catch (\Exception $ex) {
			
		}

	}

}