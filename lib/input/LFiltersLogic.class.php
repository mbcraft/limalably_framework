<?php


class LFiltersLogic {

	const SESSION_FILTERS_KEY = "/__filters";

	static function has_filter($filter_group,$name) {
		return LSession::has(self::SESSION_FILTERS_KEY.'/'.$filter_group.'/'.$name);
	}

	static function get_filter($filters_group,$name) {
		return LSession::get(self::SESSION_FILTERS_KEY.'/'.$filter_group.'/'.$name);
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

		$all = $input->get('/');

		var_dump($all);

		exit(1);

		$redirect_to_after_apply = $input->get('/redirect_to_after_apply');
		$input->remove('/redirect_to_after_apply');

		die($redirect_to_after_apply);

		$all_filters = $input->get('/');

		LSession::set(self::SESSION_FILTERS_KEY.'/'.$apply_filters_name,$all_filters);

		return new LHttpRedirect($redirect_to_after_apply);

	}

}