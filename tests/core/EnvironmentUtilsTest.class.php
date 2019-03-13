<?php

class EnvironmentUtilsTest extends LTestCase {

    function getLanguageArrayFromLanguageString($string) {
        $lang_corrected = str_replace('-', '_', $string);
        $lang_corrected = str_replace(',', ';', $lang_corrected);
        $lang_parts = explode(';', $lang_corrected);
        $langs_array = [];
        $current_langs = [];
        foreach ($lang_parts as $lang_tok) {
            if (strpos($lang_tok, 'q=') === 0) {
                $i_val = 10-(substr($lang_tok, 2) * 10);
                $langs_array[$i_val] = $current_langs;
                $current_langs = [];
            } else {
                $current_langs[] = $lang_tok;
            }
        }
        $final_result = [];
        foreach ($langs_array as $k => $val_array) {
            array_push($final_result, $val_array);
        }
        return $final_result;
    }

    function testLanguageAlg1() {
        $lang_string = 'en-US,en;q=0.9,it;q=0.8';
        $lang_array = $this->getLanguageArrayFromLanguageString($lang_string);
        
        $this->assertEqual(count($lang_array),3,"Il numero di lingue atteso non corrisponde");
        $this->assertEqual($lang_array[0],'en_US',"La prima lingua non corrisponde");
        $this->assertEqual($lang_array[1],'en',"La seconda lingua non corrisponde");
        $this->assertEqual($lang_array[2],'it',"La terza lingua non corrisponde"); 
    }
    
    function testLanguageAlg2() {
        $lang_string = 'it;q=0.8,en-US,en;q=0.9';
        $lang_array = $this->getLanguageArrayFromLanguageString($lang_string);
        
        $this->assertEqual(count($lang_array),3,"Il numero di lingue atteso non corrisponde");
        $this->assertEqual($lang_array[0],'en_US',"La prima lingua non corrisponde");
        $this->assertEqual($lang_array[1],'en',"La seconda lingua non corrisponde");
        $this->assertEqual($lang_array[2],'it',"La terza lingua non corrisponde"); 
    }

}
