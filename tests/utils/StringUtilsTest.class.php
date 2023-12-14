<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class StringUtilsTest extends LTestCase {
    
    function testRemoveInitialWww()
    {
        $current_host = "www.mbcraft.it";
        $no_www = substr($current_host,4);

        $this->assertTrue(strpos($current_host,"www.")===0,"La posizione del www non e' stata rilevata correttamente!!");
        $this->assertEqual("mbcraft.it",$no_www,"La rimozione della stringa iniziale www non e' andata a buon fine!!");
    }

    function testUnderscoreToCamelCase()
    {
        $this->assertEqual(LStringUtils::underscoredToCamelCase("contenuti_testuali"),"ContenutiTestuali","Il ritorno a camelcase non funziona correttamente!! : ".LStringUtils::underscoredToCamelCase("contenuti_testuali"));
        $this->assertEqual(LStringUtils::underscoredToCamelCase("gallery"),"Gallery","Il ritorno a camelcase non funziona correttamente!! : ".LStringUtils::underscoredToCamelCase("gallery"));
        $this->assertEqual(LStringUtils::underscoredToCamelCase("camel_case_test"),"CamelCaseTest","Il ritorno a camelcase non funziona correttamente!! : ".LStringUtils::underscoredToCamelCase("camel_case_test"));

    }

    function testPregReplace()
    {
        $test_string = "This is a/test_string";
        $result = preg_replace("/[\/ ]/","_",$test_string);

        $this->assertEqual($result,"This_is_a_test_string","Il replace non e' stato effettuato correttamente!!");
    }

    function testCamelCaseSplit()
    {
        $this->assertEqual(LStringUtils::camelCaseSplit("FPDF"),"fpdf","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("FPDF"));
        $this->assertEqual(LStringUtils::camelCaseSplit("ContenutiTestualiController"),"contenuti_testuali_controller","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("ContenutiTestualiController"));
        $this->assertEqual(LStringUtils::camelCaseSplit("ContenutiTestualiDO"),"contenuti_testuali_do","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("ContenutiTestualiDO"));


        $this->assertEqual(LStringUtils::camelCaseSplit("FPDF",true),"","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("FPDF"));
        $this->assertEqual(LStringUtils::camelCaseSplit("ContenutiTestualiController",true),"contenuti_testuali","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("ContenutiTestualiController"));
        $this->assertEqual(LStringUtils::camelCaseSplit("ContenutiTestualiDO",true),"contenuti_testuali","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("ContenutiTestualiDO"));

        $this->assertEqual(LStringUtils::camelCaseSplit("FPDF",true,"^^"),"","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("FPDF"));
        $this->assertEqual(LStringUtils::camelCaseSplit("ContenutiTestualiController",true,"^^"),"contenuti^^testuali","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("ContenutiTestualiController"));
        $this->assertEqual(LStringUtils::camelCaseSplit("ContenutiTestualiDO",true,"*"),"contenuti*testuali","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("ContenutiTestualiDO"));

        $this->assertEqual(LStringUtils::camelCaseSplit("FPDFController"),"fpdf_controller","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("FPDFController"));
        $this->assertEqual(LStringUtils::camelCaseSplit("FPDFController",true),"fpdf","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("FPDFController",true));

        $this->assertEqual(LStringUtils::camelCaseSplit("XSportBlastController"),"xsport_blast_controller","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("XSportBlastController"));
        $this->assertEqual(LStringUtils::camelCaseSplit("XSportBlastController",true),"xsport_blast","Lo split delle stringhe in camelcase non funziona correttamente!! : ".LStringUtils::camelCaseSplit("XSportBlastController",true));

    }
    
    function testContains()
    {
        $this->assertTrue(LStringUtils::contains("ProvaDiRegistrazione",["Qualcosa","Prova"]),"Il controllo di fine stringa non e' corretto!!");
        $this->assertFalse(LStringUtils::contains("ProvaDiRegistrazione",["zionerr"]),"Il controllo di fine stringa non e' corretto!!");
        $this->assertFalse(LStringUtils::contains("ProvaDiRegistrazione",["Regicstrazione","Dci"]),"Il controllo di fine stringa non e' corretto!!");
    }
    
    function testStartsWith()
    {
        $this->assertTrue(LStringUtils::startsWith("ProvaDiRegistrazione",["Qualcosa","Prova"]),"Il controllo di fine stringa non e' corretto!!");
        $this->assertFalse(LStringUtils::startsWith("ProvaDiRegistrazione",["zionerr"]),"Il controllo di fine stringa non e' corretto!!");
        $this->assertFalse(LStringUtils::startsWith("ProvaDiRegistrazione",["Registrazione","Di"]),"Il controllo di fine stringa non e' corretto!!");
    }

    function testEndsWith()
    {
        $this->assertTrue(LStringUtils::endsWith("ProvaDiRegistrazione","Registrazione"),"Il controllo di fine stringa non e' corretto!!");
        $this->assertFalse(LStringUtils::endsWith("ProvaDiRegistrazione","zionerr"),"Il controllo di fine stringa non e' corretto!!");
        $this->assertFalse(LStringUtils::endsWith("ProvaDiRegistrazione","Prova"),"Il controllo di fine stringa non e' corretto!!");
    }
    
    function testEndsWith2()
    {
        $this->assertTrue(LStringUtils::endsWith("ProvaDiRegistrazione",["Test","Calcolo","Registrazione"]),"Il controllo di fine stringa non e' corretto!!");
        $this->assertFalse(LStringUtils::endsWith("ProvaDiRegistrazione",["zionerr","quasimodo","alcuna"]),"Il controllo di fine stringa non e' corretto!!");
        $this->assertFalse(LStringUtils::endsWith("ProvaDiRegistrazione",["Prova","Di","Registraz"]),"Il controllo di fine stringa non e' corretto!!");
    }
    
    function testEndsWith3() {
        
        $this->assertTrue(LStringUtils::endsWith("/html/tester/index.html", LFormat::HTML),"La stringa non viene rilevata correttamente!");
        
    }

    function testTrimEndingChars()
    {
        $this->assertEqual(LStringUtils::trimEndingChars("ProvaDiABC",3),"ProvaDi","Il trim degli ultimi caratteri non e' corretto!!");
        $this->assertEqual(LStringUtils::trimEndingChars("ProvaDiABC",5),"Prova","Il trim degli ultimi caratteri non e' corretto!!");
        $this->assertEqual(LStringUtils::trimEndingChars("ProvaDiABC",10),"","Il trim degli ultimi caratteri non e' corretto!!");
        try
        {
            LStringUtils::trimEndingChars("Ciao",5);
            $this->fail("Numero di caratteri piu' lungo della stringa.");
        }
        catch(Exception $ex)
        {
            //ok
        }
    }

    function testReplacementStringCalculations() {

        $var_name = "THISISAVAR";

        $result = LStringUtils::getCommentDelimitedReplacementsStringSeparator($var_name);

        $this->assertEqual('T_H_I_S_I_S_A_V_A_R',$result,"Il risultato dell'elaborazione non coincide col valore atteso!");

    }
    
}
