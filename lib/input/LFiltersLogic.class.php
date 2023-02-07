<?php

class LFiltersLogic {

	const SESSION_FILTERS_KEY = "/__filters";

	private static $filter_group;

	static function init($filter_group) {
		self::$filter_group = $filter_group;

		return array();
	}

	static function has_filters() {
		return LSession::has(self::SESSION_FILTERS_KEY.'/'.self::$filter_group);
	}

	static function has_filter($name) {
		$in_session = LSession::has(self::SESSION_FILTERS_KEY.'/'.self::$filter_group.$name);
		$in_input = LInput::has($name);

		return $in_session || $in_input;
	}

	static function get_filter_value($name) {
		if (LSession::has(self::SESSION_FILTERS_KEY.'/'.self::$filter_group.$name))
			return LSession::get(self::SESSION_FILTERS_KEY.'/'.self::$filter_group.$name);
		return LInput::get($name);
	}

	function reset_filters($input) {

		$reset_filters_name = $input->get('/reset_filters_name');

		$redirect_to_after_reset = $input->get('/redirect_to_after_reset');

		LSession::remove(self::SESSION_FILTERS_KEY.'/'.$reset_filters_name);

		return new LHttpRedirect($redirect_to_after_reset);
	}

	function apply_filters($input) {

		$apply_filters_name = $input->get('/apply_filters_name');
		$input->remove('/apply_filters_name');

		$redirect_to_after_apply = $input->get('/redirect_to_after_apply');
		$input->remove('/redirect_to_after_apply');

		$all_filters = $input->get('/');

		LSession::set(self::SESSION_FILTERS_KEY.'/'.$apply_filters_name,$all_filters);

		return new LHttpRedirect($redirect_to_after_apply);

	}

}