<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


interface LIDbConnection {
    
    function getName();

    function isOpen();
    
    function open();
    
    function close();
    
    function getHandle();

    function beginTransaction();

    function rollback();

    function commit();

    function setCharset($charset_name);
    
}
