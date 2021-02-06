<?php

interface LIDbConnection {
    
    function isOpen();
    
    function open();
    
    function close();
    
    function getHandle();
    
}
