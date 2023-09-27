<?php


class LTimeCaptcha {


	static function create() {

		$captcha_time = time();

		$captcha_password = LConfigReader::simple("/captcha/password");

		$captcha_security = sha1($captcha_password.'_'.$captcha_time);

		return array(
			'captcha_time' => $captcha_time,
			'captcha_security' => $captcha_security
		);

	}

	static function parse($captcha_time,$captcha_security) {

		$captcha_password = LConfigReader::simple("/captcha/password");

		$calculated_captcha_security = sha1($captcha_password.'_'.$captcha_time);
	
		$wait_time = LConfigReader::simple("/captcha/wait_time");

		$current_time = time();

		$final_time = $captcha_time + $wait_time;

		if ($current_time>$final_time) {
			
			if ($calculated_captcha_security != $captcha_security) return false;

		}
		else {

			$actual_wait_seconds = $final_time - $current_time;

			sleep($actual_wait_seconds);

			if ($calculated_captcha_security != $captcha_security) return false;

		}

		return true;

	}

}