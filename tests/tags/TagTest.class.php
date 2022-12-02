<?php



class TagTest extends LTestCase {
	
	function testVariousRenderingModes() {
		$p1 = new LTag('abcd');
		$p1->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p1->setIndentMode(LTag::INDENT_MODE_SKIP_ALL);

		$p1->my_attribute("my_value");
		$p1[] = "This is my content";

		$this->assertEqual("".$p1,'<abcd my_attribute="my_value" >This is my content</abcd>',"Il rendering del tag non è corretto!");
	}

	function testParent() {

		$p1 = new LTag('abcd');
		$p1->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p1->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$c1 = new LTag('efgh');
		$c1->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$c1->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$p1->add($c1);
		$this->assertEqual($c1->getParent(),$p1,"Il parent non è stato sistemato correttamente!!");

		$p2 = new LTag('abcd');
		$p2->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p2->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$c2 = new LTag('efgh');
		$c2->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$c2->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$p2[] = $c2;
		$this->assertEqual($c2->getParent(),$p2,"Il parent non è stato sistemato correttamente!!");

		$p3 = new LTag('abcd');
		$p3->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p3->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$c3 = new LTag('efgh');
		$c3->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$c3->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$p3->my_child = $c3;
		$this->assertEqual($c3->getParent(),$p3,"Il parent non è stato sistemato correttamente!!");


		$p4 = new LTag('abcd');
		$p4->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p4->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$c4 = new LTag('efgh');
		$c4->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$c4->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$p4->ch_testChild($c4);
		$this->assertEqual($c4->getParent(),$p4,"Il parent non è stato sistemato correttamente!!");


	}

}