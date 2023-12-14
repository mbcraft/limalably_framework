<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class THtmlDoc extends LJAbstractTemplatePart {
	
	const TEMPLATE_FIELDS = ["head","body"];

	const MANDATORY_FIELDS = ["head","body"];

	function render() {
		
		$tl = new LTagList();

		$tl[] = tag('doctype_html');

		$lang = 'it';

		if (LSession::has('/user/current_lang')) {
			$lang = LSession::get('/user/current_lang');
		} else {
			if (LSession::has('/current_lang')) $lang = LSession::get("/current_lang");
		}

		$html = tag('html')->lang($lang);

		$html[] = $this->head;
		$html[] = $this->body;

		$tl[] = $html;

		return $tl;
	}

}