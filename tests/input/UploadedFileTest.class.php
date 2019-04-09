<?php

class UploadedFileTest extends LTestCase {
    
    const UPLOAD_NESTED_1 = array ( 'upload' => array ( 'name' => array ( 'file1' => 'Analisi.odt', 'file2' => '', ), 'type' => array ( 'file1' => 'application/vnd.oasis.opendocument.text', 'file2' => '', ), 'tmp_name' => array ( 'file1' => '/tmp/php0L6p8y', 'file2' => '', ), 'error' => array ( 'file1' => 0, 'file2' => 4, ), 'size' => array ( 'file1' => 36404, 'file2' => 0, ), ), );
    const UPLOAD_NESTED_2 = array ( 'upload' => array ( 'name' => array ( 'again' => array ( 'file1' => 'Analisi.odt', 'file2' => '', ), ), 'type' => array ( 'again' => array ( 'file1' => 'application/vnd.oasis.opendocument.text', 'file2' => '', ), ), 'tmp_name' => array ( 'again' => array ( 'file1' => '/tmp/php62V3Zq', 'file2' => '', ), ), 'error' => array ( 'again' => array ( 'file1' => 0, 'file2' => 4, ), ), 'size' => array ( 'again' => array ( 'file1' => 36404, 'file2' => 0, ), ), ), );
    const UPLOAD_SIMPLE = array ( 'file1' => array ( 'name' => 'Analisi.odt', 'type' => 'application/vnd.oasis.opendocument.text', 'tmp_name' => '/tmp/php1Rjefo', 'error' => 0, 'size' => 36404, ), 'file2' => array ( 'name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0, ), );
    
    function testUploadSimple() {
        
        $normalized_data = LUploadedFile::normalizeArray(self::UPLOAD_SIMPLE);
        
        $this->assertTrue($normalized_data['file1'] instanceof LUploadedFile,"I dati non sono stati normalizzati correttamente!");
        $this->assertTrue($normalized_data['file2'] instanceof LUploadedFile,"I dati non sono stati normalizzati correttamente!");
    
        $this->assertEqual(count($normalized_data),2,"Il numero di valori normalizzati non corrisponde!");
    }
    
    function testUploadNested1() {
        $normalized_data = LUploadedFile::normalizeArray(self::UPLOAD_NESTED_1);
        
        $this->assertTrue($normalized_data['upload']['file1'] instanceof LUploadedFile,"I dati non sono stati normalizzati correttamente!");
        $this->assertTrue($normalized_data['upload']['file2'] instanceof LUploadedFile,"I dati non sono stati normalizzati correttamente!");
    
        $this->assertEqual(count($normalized_data['upload']),2,"Il numero di valori normalizzati non corrisponde!");
    }
    
    function testUploadNested2() {
        $normalized_data = LUploadedFile::normalizeArray(self::UPLOAD_NESTED_2);
        
        $this->assertTrue($normalized_data['upload']['again']['file1'] instanceof LUploadedFile,"I dati non sono stati normalizzati correttamente!");
        $this->assertTrue($normalized_data['upload']['again']['file2'] instanceof LUploadedFile,"I dati non sono stati normalizzati correttamente!");
    
        $this->assertEqual(count($normalized_data['upload']['again']),2,"Il numero di valori normalizzati non corrisponde!");
        
        
    }
    
}
