<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LPaginator {
	
	const FULL_PAGE_LIST_PAGE_LIMIT = 11;

	const ALLOWED_PAGE_SIZES = [10,25,50,75,100];

	const DEFAULT_PAGE_SIZE = 25;

	const SESSION_PAGINATION_KEY = '/__pagination';

	private $paginator_name;

	private $link_base;
	private $items_count;
	private $page_size;

	private $current_page;
	private $page_count;

	private $page_items = [];
	private $psize_items = [];

	private $setup_pagination_done = false;
	private $setup_sizer_done = false;

	function __construct($paginator_name,$link_base,$items_count) {
		
		$this->paginator_name = $paginator_name;

		$this->link_base = $link_base.'?paginator='.$paginator_name;

		$this->items_count = $items_count;

		$this->loadPageSize();
		$this->loadCurrentPage();

		if (LInput::has('/paginator') && LInput::get('/paginator')==$this->paginator_name) {
			if (LInput::has('/current_page')) {
				
				$this->current_page = LInput::get('/current_page');
				$this->saveCurrentPage();

			}
			if (LInput::has('/page_size')) {
				
				$this->page_size = LInput::get('/page_size');
				$this->savePageSize();

				$this->current_page = 1;
				$this->saveCurrentPage();
			}
		} 
		

		$this->page_count = ceil($this->items_count/$this->page_size); 
	
	}

	public static function removeCurrentPage($paginator_name) {
		if (LSession::has(self::SESSION_PAGINATION_KEY.'/'.$paginator_name.'/current_page'))
			LSession::remove(self::SESSION_PAGINATION_KEY.'/'.$paginator_name.'/current_page');
	}

	private function saveCurrentPage() {

		LSession::set(self::SESSION_PAGINATION_KEY.'/'.$this->paginator_name.'/current_page',$this->current_page);

	}

	private function loadCurrentPage() {
		$cp = 1;

		if (LSession::has(self::SESSION_PAGINATION_KEY.'/'.$this->paginator_name.'/current_page'))
			$cp = LSession::get(self::SESSION_PAGINATION_KEY.'/'.$this->paginator_name.'/current_page');

	    $this->current_page = $cp;
	}

	public function getCurrentPage() {
		return $this->current_page;
	}

	private function savePageSize() {
		
		LSession::set(self::SESSION_PAGINATION_KEY.'/'.$this->paginator_name.'/page_size',$this->page_size);

	}

	private function loadPageSize() {

		$ps = self::DEFAULT_PAGE_SIZE;

		if (LSession::has(self::SESSION_PAGINATION_KEY.'/'.$this->paginator_name.'/page_size')) {
			$ps = LSession::get(self::SESSION_PAGINATION_KEY.'/'.$this->paginator_name.'/page_size');
		}

		$this->page_size = $ps;
	}

	public function getPageSize() {
		return $this->page_size;
	}

	public function getPageCount() {
		return $this->page_count;
	}

	private function setupPaginationItems() {

		if ($this->setup_pagination_done) return;
		$this->setup_pagination_done = true;

		if ($this->page_count<=1) return;

		if ($this->page_count<self::FULL_PAGE_LIST_PAGE_LIMIT) $this->setupAllPagesPaginator();
		else $this->setupPartialPaginator();
	}


	private function setupPageSizesItems() {

		if ($this->setup_sizer_done) return;
		$this->setup_sizer_done = true;

		foreach (self::ALLOWED_PAGE_SIZES as $ps) {

			$item = new LPaginatorItem();
			
			$item->label = $ps;
			if ($this->getPageSize()==$ps) {
				$item->link = "";
				$item->active = true;
			} else {
				$item->link = $this->link_base.'&page_size='.$ps;
			}

			$this->psize_items [] = $item;

		}

	}

	public function getPaginationItems() {

		$this->setupPaginationItems();

		return $this->page_items;
	}

	public function getPageSizesItems() {

		$this->setupPageSizesItems();

		return $this->psize_items;

	}

	private function setupAllPagesPaginator() {

		$previous = new LPaginatorItem();
		$previous->label = "Previous";
		if ($this->current_page==1) {
			$previous->disabled = true;
			$previous->link = "";
		} else {
			$previous->link = $this->link_base.'&current_page='.($this->current_page-1);
		}

		$this->page_items [] = $previous;

		for ($i=1;$i<=$this->page_count;$i++) {

			$item = new LPaginatorItem();
			$item->label = "".$i;
			if ($this->current_page==$i) {
				$item->active = true;
				$item->link = "";
			} else {
				$item->link = $this->link_base."&current_page=".$i;
			}

			$this->page_items [] = $item;
		}

		$next = new LPaginatorItem();
		$next->label = "Next";
		if ($this->current_page==$this->page_count) {
			$next->disabled = true;
			$next->link = "";
		} else {
			$next->link = $this->link_base.'&current_page='.($this->current_page+1);
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
			$first->link = $this->link_base.'&current_page=1';
		}

		$this->page_items [] = $first;

		$previous = new LPaginatorItem();
		$previous->label = "Previous";
		if ($this->current_page==1) {
			$previous->disabled = true;
			$previous->link = "";
		} else {
			$previous->link = $this->link_base.'&current_page='.($this->current_page-1);
		}

		$this->page_items [] = $previous;


		for ($i=1;$i<=3;$i++) {

			$item = new LPaginatorItem();
			$item->label = $i;
			if ($this->current_page==$i) {
				$item->active = true;
				$item->link = "";
			} else {
				$item->link = $this->link_base.'&current_page='.$i;
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
				$item->link = $this->link_base.'&current_page='.$i;
			}

			$this->page_items [] = $item;
		}

		$next = new LPaginatorItem();
		$next->label = "Next";
		if ($this->current_page==$this->page_count) {
			$next->disabled = true;
			$next->link = "";
		} else {
			$next->link = $this->link_base.'&current_page='.($this->current_page+1);
		}

		$this->page_items [] = $next;

		$last = new LPaginatorItem();
		$last->label = "Last";
		if ($this->current_page==$this->page_count) {
			$last->disabled = true;
			$last->link = "";
		} else {
			$last->link = $this->link_base.'&current_page='.$this->page_count;
		}

		$this->page_items [] = $last;

	}



}