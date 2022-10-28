<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LRandomUtils {
	
	const ALL_DIGITS = ['0','1','2','3','4','5','6','7','8','9'];

	const ALL_LETTERS = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','w','x','y','z'];



	public static function seed(int $seed=0) {
		if (!$seed) srand(time());
		else srand($seed);
	}

	public static function digitCode(int $size) {

		$all_digits_size = count(self::ALL_DIGITS);

		$result = "";

		for ($i=0;$i<$size;$i++) {
			$result.= self::ALL_DIGITS[rand(0,$all_digits_size-1)];
		}

		return $result;
	}

	public static function letterCode(int $size) {
		$all_letters_size = count(self::ALL_LETTERS);

		$result = "";

		for ($i=0;$i<$size;$i++) {
			$result.= self::ALL_LETTERS[rand(0,$all_letters_size-1)];
		}

		return $result;
	}

}