<?php

class TwigFactoryTest extends LTestCase {
    
    function testSimpleCache() {
        
        $f = new LTwigTemplateSourceFactory();
        $f->init($_SERVER['FRAMEWORK_DIR']);
        
        $data_map = ['hello/test' => 'Hello {{ name }}!!','my_name' => 'My name is {{ name }} ... what is yours?'];
        
        $template_source = $f->createStringArrayTemplateSource($data_map, 'tests/template/cache/');
        
        $this->assertEqual($template_source->searchTemplate('hello/test'),'hello/test',"Il template hello non è stato trovato!");
        
        $hello_template = $template_source->getTemplate('hello/test');
        
        $result = $hello_template->render(['name' => 'Abcdefg']);
        
        $this->assertEqual($result,'Hello Abcdefg!!',"Il risultato del template non corrisponde!! : ".$result);
        
        $hello_template = $template_source->getTemplate('hello/test');
        
        $result = $hello_template->render(['name' => 'Abcdefg']);
        
        $this->assertEqual($result,'Hello Abcdefg!!',"Il risultato del template non corrisponde!!");
        
    }
    
}