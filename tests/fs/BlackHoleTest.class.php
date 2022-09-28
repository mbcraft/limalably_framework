<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class BlackHoleTest extends LTestCase
{
    function testBlackHole()
    {
        $f = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/BlackHoleTest.class.php");

        $this->assertTrue($f->exists(),"Il file del test non esiste!!");

        $content = $f->getContent();

        $f->delete();

        $this->assertFalse($f->exists(),"Il file del test black hole non e' stato eliminato!!");

        $f->touch();

        $f->setContent($content);

        $this->assertTrue($f->exists(),"Il file del test black hole non e' stato rigenerato!!");


    }
}

?>