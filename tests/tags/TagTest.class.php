<?php



class TagTest extends LTestCase {
	
	function testIndentMode() {
		$sa1 = new LTag('abcd');
		$sa1->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$sa1->setIndentMode(LTag::INDENT_MODE_SKIP_ALL);

		$sa1->my_attribute("my_value");
		$sa1[] = "This is my content";

		$this->assertEqual("".$sa1,'<abcd my_attribute="my_value" >This is my content</abcd>',"Il rendering del tag non è corretto!");

		$sa2 = new LTag('abcd');
		$sa2->setTagMode(LTag::TAG_MODE_OPENCLOSE_NO_CONTENT);
		$sa2->setIndentMode(LTag::INDENT_MODE_SKIP_ALL);

		$sa2->my_attribute("my_value");

		$this->assertEqual("".$sa2,'<abcd my_attribute="my_value" />',"Il rendering del tag non è corretto!");

		$sa3 = new LTag('abcd');
		$sa3->setTagMode(LTag::TAG_MODE_OPEN_ONLY);
		$sa3->setIndentMode(LTag::INDENT_MODE_SKIP_ALL);

		$sa3->my_attribute("my_value");

		$sa4 = new LTag('abcd');
		$sa4->setTagMode(LTag::TAG_MODE_OPEN_EMPTY_CLOSE);
		$sa4->setIndentMode(LTag::INDENT_MODE_SKIP_ALL);

		$sa4->my_attribute("my_value");

		$this->assertEqual("".$sa4,'<abcd my_attribute="my_value" ></abcd>',"Il rendering del tag non è corretto!");

		$p1 = new LTag('abcd');
		$p1->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p1->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$p1->my_attribute("my_value");
		$p1[] = "This is my content";

		$this->assertEqual("".$p1,'<abcd my_attribute="my_value" >'."\r\n\t".'This is my content'."\r\n".'</abcd>',"Il rendering del tag non è corretto!");

		$p2 = new LTag('abcd');
		$p2->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p2->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$p2->attr1("value1");
		
		$p3 = new LTag('efgh');
		$p3->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p3->setIndentMode(LTag::INDENT_MODE_NORMAL);

		$p3->attr2("value2");
		$p3[] = "This is my content";
		$p2[] = $p3;

		$this->assertEqual("".$p2,'<abcd attr1="value1" >'."\r\n\t".'<efgh attr2="value2" >'."\r\n\t\tThis is my content\r\n\t</efgh>\r\n".'</abcd>',"Il rendering del tag non è corretto!");

		$p4 = new LTag('abcd');
		$p4->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p4->setIndentMode(LTag::INDENT_MODE_SKIP_ALL);

		$p4->attr1("value1");
		
		$p5 = new LTag('efgh');
		$p5->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p5->setIndentMode(LTag::INDENT_MODE_SKIP_ALL);

		$p5->attr2("value2");
		$p5[] = "This is my content";
		$p4[] = $p5;

		$this->assertEqual("".$p4,'<abcd attr1="value1" ><efgh attr2="value2" >This is my content</efgh></abcd>',"Il rendering del tag non è corretto!");
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

	public function testBasicAttributes() {

		$p1 = new LTag('abcd');
		$p1->setTagMode(LTag::TAG_MODE_OPEN_CONTENT_CLOSE);
		$p1->setIndentMode(LTag::INDENT_MODE_SKIP_ALL);

		$p1->my_attribute("first_value");
		$p1->data__info("second_value");
		$p1->data__test__mode('Something "cute"!');
		$p1->another§attribute('another_value');

		$p1[] = "This is my content";

		$this->assertEqual("".$p1,'<abcd my_attribute="first_value" data-info="second_value" data-test-mode="Something \'cute\'!" another__attribute="another_value" >This is my content</abcd>',"Il rendering del tag non è corretto!");

	}

}