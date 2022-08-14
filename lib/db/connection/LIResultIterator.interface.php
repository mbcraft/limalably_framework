<?php


interface LIResultIterator {
	
	function hasMore();

	function nextRow();

	fuction stop();

}