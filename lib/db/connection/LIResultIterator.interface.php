<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


interface LIResultIterator {
	
	function hasMore();

	function nextRow();

	fuction stop();

}