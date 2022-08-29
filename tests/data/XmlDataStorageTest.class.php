<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class XmlDataStorageTest extends LTestCase {
    
    function testGet() {
        
        $storage = new LXmlDataStorage();
        $storage->init($_SERVER['FRAMEWORK_DIR']);
        
        $data = $storage->load('tests/data/my_data3');
        
        $this->assertEqual($data['qualcosa']['uno'],42,"Il dato letto non corrisponde!");
        $this->assertEqual($data['qualcosa']['ancora']['due'],"Ciao","Il dato letto non corrisponde!");
        
        $ancora3 = $data['qualcosa']['ancora']['tre'];
        
        $this->assertTrue(LStringUtils::contains($ancora3, '<br>'), "L'html non è stato letto correttamente!");
        $this->assertTrue(LStringUtils::contains($ancora3, '<hr>'), "L'html non è stato letto correttamente!");
        $this->assertTrue(LStringUtils::contains($ancora3, 'Questo è un testo di prova'), "L'html non è stato letto correttamente!");
        $this->assertTrue(LStringUtils::contains($ancora3, '<div>'), "L'html non è stato letto correttamente!");
        $this->assertTrue(LStringUtils::contains($ancora3, '</div>'), "L'html non è stato letto correttamente!");
        
    }

    function testSaveLoadData() {
        $storage = new LXmlDataStorage();
        $storage->init($_SERVER['FRAMEWORK_DIR']);
        
        $storage->init('tests/tmp/');

        $test_html = <<<EOH

<span>Hello world</span>

EOH;

        $data = ['a' => 1,'b' => 2,'c' => 'x','d' => $test_html];

        $storage->delete('prova');

        $this->assertFalse($storage->isSaved('prova'),"I dati sono ancora presenti!");

        $storage->save('prova',$data);

        $this->assertTrue($storage->isSaved('prova'),"I dati sono ancora presenti!");

        $loaded_data = $storage->load('prova');

        $this->assertEqual($loaded_data['a'],1,"I dati letti non corrispondono!");
        $this->assertEqual($loaded_data['b'],2,"I dati letti non corrispondono!");
        $this->assertEqual($loaded_data['c'],'x',"I dati letti non corrispondono!");
        $this->assertEqual($loaded_data['d'],$test_html,"L'html letto dall'xml non corrisponde a quello salvato!");

        //$storage->delete('prova');
    }


    private function hasTags(string $value) {
        $matches = [];

        preg_match_all("/(\<(?<closing>\/?)(?<tagname>\w+)\s*(?<autoclose>\/?)\>)/",$value,$matches, PREG_UNMATCHED_AS_NULL);

        $match_count = count($matches[0]);

        return $match_count>0;
    }

    private function hasWellFormedTags(string $value) {

        $matches = [];

        preg_match_all("/(\<(?<closing>\/?)(?<tagname>\w+)\s*(?<autoclose>\/?)\>)/",$value,$matches, PREG_UNMATCHED_AS_NULL);

        $match_count = count($matches[0]);

        $tag_stack = [];

        for ($i=0;$i<$match_count;$i++) {
            $tag_name = $matches['tagname'][$i];
            $is_autoclose = $matches['autoclose'][$i]!=null;
            $is_closing = $matches['closing'][$i]!=null;
            if ($is_closing && $is_autoclose) return false;
            $is_begin = !$is_autoclose && !$is_closing;

            if ($is_autoclose) continue;
            if ($is_begin) array_push($tag_stack,$tag_name);
            if ($is_closing) {
                $current_el = array_pop($tag_stack);
                if ($current_el==$tag_name) continue;
                else return false;
            }
        
        }

        if (!empty($tag_stack)) return false;

        return true;

    }

    public function testWellFormedTags() {
        $t1 = "fkldgjgfklj <miotag /> blkijfdkdhf";
        $t2 = "<miotag> kdjshdfkjshdfs k </miotag>";
        $t3 = "qualcosa bla <opentag>";
        $t4 = "qualcosaltro </closetag> ";
        $t5 = "bla bla e ancora bla ....";

        
        $this->assertTrue($this->hasWellFormedTags($t1),"Il controllo di correttezza dell'xml non funziona correttamente!");
        $this->assertTrue($this->hasWellFormedTags($t2),"Il controllo di correttezza dell'xml non funziona correttamente!");
        $this->assertFalse($this->hasWellFormedTags($t3),"Il controllo di correttezza dell'xml non funziona correttamente!");
        $this->assertFalse($this->hasWellFormedTags($t4),"Il controllo di correttezza dell'xml non funziona correttamente!");
        $this->assertTrue($this->hasWellFormedTags($t5),"Il controllo di correttezza dell'xml non funziona correttamente!");


    }
    
}
