<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

function db($connection_name = null) {
    return LDbConnectionManager::get($connection_name);
}