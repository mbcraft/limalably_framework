<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LCaptchaSource {

	static function create() {

		$captcha_time = time();

		$captcha_password = LConfigReader::simple("/captcha/password");

		$captcha_security = sha1($captcha_password.'_'.$captcha_time);

		return array(
			'captcha_time' => $captcha_time,
			'captcha_security' => $captcha_security
		);

	}

}