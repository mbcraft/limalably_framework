<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LFluxCaptcha {

	static function parse($captcha_time,$captcha_security) {
		
		$captcha_password = LConfigReader::simple("/captcha/password");

		$calculated_captcha_security = sha1($captcha_password.'_'.$captcha_time);

		if ($captcha_security != $calculated_captcha_security) return false;

		$db = db();

		insert('flux_captcha',['login_try_timestamp'],[$captcha_time])->go($db);

		$timestamp_lower_limit = $captcha_time - LConfigReader::simple('/captcha/flux_time_range');

		delete('flux_captcha')->where(_lt('login_try_timestamp',$timestamp_lower_limit))->go($db);

		$detected_flux_count = select('count(*) AS C','flux_captcha')->go($db)[0]['C'];

		$flux_limit = LConfigReader::simple('/captcha/flux_limit');

		if ($detected_flux_count > $flux_limit) return false;

		return true;
	}

	static function migration_execute() {

		$db = db();

		create_table('flux_captcha')->if_not_exists()
		->column(col_def('id')->t_id())
		->column(col_def('login_try_timestamp')->t_u_bigint()->not_null())
		->engine_memory()
		->go($db);
	}

	static function migration_revert() {

		$db = db();

		drop_table('flux_captcha')->if_exists()->go($db);
	}
	
}