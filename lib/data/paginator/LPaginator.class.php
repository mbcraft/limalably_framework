<?php


class LPaginator {
	

	const FULL_PAGE_LIST_PAGE_LIMIT = 11;

	const DEFAULT_PAGE_SIZE = 25;

	private $link_base;
	private $items_count;
	private $page_size;

	private $current_page;
	private $page_count;

	function __construct($link_base,$items_count,$page_size=self::DEFAULT_PAGE_SIZE) {
		$this->link_base = $link_base;
		$this->items_count = $items_count;
		$this->page_size = $page_size;
	
		$this->current_page = 1;

		if (LInput::has('/current_page'))
			$this->current_page = LInput::get('/current_page'); 

		$this->page_count = ceil($this->items_count/$this->page_size); 

		$this->setup();
	}

	public function getPageCount() {
		return $this->page_count;
	}

	private function setup() {

		if ($this->page_count<self::FULL_PAGE_LIST_PAGE_LIMIT) $this->setupAllPagesPaginator();
		else $this->setupPartialPaginator();
	}

	public function getPaginationItems() {

		return $this->page_items;
	}

	private function setupAllPagesPaginator() {

		$previous = new LPaginatorItem();
		$previous->label = "Previous";
		if ($this->current_page==1) {
			$previous->disabled = true;
			$previous->link = "";
		} else {
			$previous->link = $this->link_base.'?current_page='.($this->current_page-1);
		}

		$this->page_items [] = $previous;

		for ($i=1;$i<=$this->page_count;$i++) {

			$item = new LPaginatorItem();
			$item->label = "".$i;
			if ($this->current_page==$i) {
				$item->active = true;
				$item->link = "";
			} else {
				$item->link = $this->link_base."?current_page=".$i;
			}

			$this->page_items [] = $item;
		}

		$next = new LPaginatorItem();
		$next->label = "Next";
		if ($this->current_page==$this->page_count) {
			$next->disabled = true;
			$next->link = "";
		} else {
			$next->link = $this->link_base.'?current_page='.($this->current_page+1);
		}

		$this->page_items [] = $next;

	}

	private function setupPartialPaginator() {

		$first = new LPaginatorItem();
		$first->label = "First";
		if ($this->current_page==1) {
			$first->disabled = true;
			$first->link = "";
		} else {
			$first->link = $this->link_base.'?current_page=1';
		}

		$this->page_items [] = $first;

		$previous = new LPaginatorItem();
		$previous->label = "Previous";
		if ($this->current_page==1) {
			$previous->disabled = true;
			$previous->link = "";
		} else {
			$previous->link = $this->link_base.'?current_page='.($this->current_page-1);
		}

		$this->page_items [] = $previous;


		for ($i=1;$i<=3;$i++) {

			$item = new LPaginatorItem();
			$item->label = $i;
			if ($this->current_page==$i) {
				$item->active = true;
				$item->link = "";
			} else {
				$item->link = $this->link_base.'?current_page='.$i;
			}

			$this->page_items [] = $item;
		}

		if ($this->current_page>4) {
			$item = new LPaginatorItem();
			$item->label = "...";
			$item->link = "";
			$item->disabled = true;

			$this->page_items [] = $item;
		}

		if ($this->current_page>3 && $this->current_page<($this->page_count-2)) {
			$p_current = new LPaginatorItem();
			$p_current->label = "".$this->current_page;
			$p_current->active = true;
			$p_current->link = "";

			$this->page_items [] = $p_current;
		}

		if ($this->current_page<$this->page_count-3) {
			$item = new LPaginatorItem();
			$item->label = "...";
			$item->link = "";
			$item->disabled = true;

			$this->page_items [] = $item;
		}

		for ($i=$this->page_count-2;$i<=$this->page_count;$i++) {

			$item = new LPaginatorItem();
			$item->label = $i;
			if ($this->current_page==$i) {
				$item->active = true;
				$item->link = "";
			} else {
				$item->link = $this->link_base.'?current_page='.$i;
			}

			$this->page_items [] = $item;
		}

		$next = new LPaginatorItem();
		$next->label = "Next";
		if ($this->current_page==$this->page_count) {
			$next->disabled = true;
			$next->link = "";
		} else {
			$next->link = $this->link_base.'?current_page='.($this->current_page+1);
		}

		$this->page_items [] = $next;

		$last = new LPaginatorItem();
		$last->label = "Last";
		if ($this->current_page==$this->page_count) {
			$last->disabled = true;
			$last->link = "";
		} else {
			$last->link = $this->link_base.'?current_page='.$this->page_count;
		}

		$this->page_items [] = $last;

	}



}