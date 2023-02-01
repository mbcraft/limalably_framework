<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class IniDatFileDataStorageTest extends LTestCase {
	

	function testSimpleIniDatStorage() {



        $d = new LIniDatFileDataStorage();
        $d->init($_SERVER['FRAMEWORK_DIR'].'tests/data/tmp/');

        $dat_path = $_SERVER['FRAMEWORK_DIR'].'tests/data/tmp/dat/';

        $storage_path = $_SERVER['FRAMEWORK_DIR'].'tests/data/tmp/prova.ifs';

        $data = ['a' => 'alpha','b' => array('k' => 'beta','g' => 'theta')];



    	$d->delete('prova');

    	$this->assertFalse(is_file($storage_path),"Il file esiste già!");
        $this->assertFalse(is_file($dat_path.'prova_1.dat'),"Il file esiste già!");
        $this->assertFalse(is_file($dat_path.'prova_2.dat'),"Il file esiste già!");
        $this->assertFalse(is_file($dat_path.'prova_3.dat'),"Il file esiste già!");
        $this->assertFalse(is_file($dat_path.'prova_4.dat'),"Il file esiste già!");

        $d->save('prova',$data);

        $this->assertTrue(is_file($storage_path),"Il file non è stato salvato!");
        $this->assertTrue(is_file($dat_path.'prova_1.dat'),"Il file non è stato salvato!");
        $this->assertEqual(file_get_contents($dat_path.'prova_1.dat'),'alpha',"Il contenuto del file non corrisponde!");
        $this->assertTrue(is_file($dat_path.'prova_2.dat'),"Il file non è stato salvato!");
        $this->assertEqual(file_get_contents($dat_path.'prova_2.dat'),'beta',"Il contenuto del file non corrisponde!");
        $this->assertTrue(is_file($dat_path.'prova_3.dat'),"Il file non è stato salvato!");
        $this->assertEqual(file_get_contents($dat_path.'prova_3.dat'),'theta',"Il contenuto del file non corrisponde!");
        $this->assertFalse(is_file($dat_path.'prova_4.dat'),"Il file non dovrebbe esserci!");

		$d->delete('prova');

		$this->assertFalse(is_file($storage_path),"Il file non è stato eliminato!");
        $this->assertFalse(is_file($dat_path.'prova_1.dat'),"Il file non è stato eliminato!");
        $this->assertFalse(is_file($dat_path.'prova_2.dat'),"Il file non è stato eliminato!");
        $this->assertFalse(is_file($dat_path.'prova_3.dat'),"Il file non è stato eliminato!");
        $this->assertFalse(is_file($dat_path.'prova_4.dat'),"Il file non è stato eliminato!");
	}


}