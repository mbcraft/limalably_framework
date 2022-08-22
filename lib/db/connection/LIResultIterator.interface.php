<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


interface LIResultIterator {
	
	function hasNext();

	function nextRow();

	function stop();

}