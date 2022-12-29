<?php



class THtmlHead extends LJAbstractTemplatePart {
	
	function render() {
	
		$head = tag('head');

		$title = "No title is set";
		$description = "No description is set";
		$keywords = "";

		if (LPageData::has('/page/title')) $title = LPageData::get('/page/title');
		if (LPageData::has('/page/description')) $description = LPageData::get('/page/description');
		if (LPageData::has('/page/keywords')) $keywords = LPageData::get('/page/keywords');

		$title = tag('title')($title);
		$head[] = $title;

		$desc = tag('meta_description')->content($description);
		$head[] = $description;

		$keywords = tag('meta_keywords')->content($keywords);
		$head[] = $keywords;

		$head[] = tag('meta_charset');
		$head[] = tag('meta_viewport');

		$head[] = tag('link_css')->href('/assets/css/bootstrap.min.css');
		$head[] = tag('script_src')->href('/assets/js/bootstrap.bundle.min.js');

		return $head;
	}

}