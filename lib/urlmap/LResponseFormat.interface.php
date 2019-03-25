<?php

interface LResponseFormat {
    
    const FORMAT_LIST = [self::FORMAT_HTML,self::FORMAT_JSON,self::FORMAT_XML,self::FORMAT_BINARY,self::FORMAT_OUTPUT,self::FORMAT_FILE_OUTPUT];
    
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';
    const FORMAT_BINARY = 'binary';
    const FORMAT_OUTPUT = 'output';
    const FORMAT_FILE_OUTPUT = 'file_output';
}
