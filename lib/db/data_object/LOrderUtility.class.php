<?php


class LOrderUtility {
	
	private $my_element;

	const MY_ORDER_COLUMN = 'order_val';

	const MY_ORDER_GROUP_COLUMNS = ['element_type','element_id'];

	function __construct($element) {
		$this->my_element = $element;
	}

	private function checkRequiredOrderingConstants() {
		if (static::MY_ORDER_COLUMN==null) throw new \Exception("Constant MY_ORDER_COLUMN is required for ordering to work.");
		if (static::MY_ORDER_GROUP_COLUMNS===null) throw new \Exception("Constant MY_ORDER_GROUP_COLUMNS is required for ordering to work.");
	}

	private function reorder_all($data) {

		$this->checkRequiredOrderingConstants();

		$order_val = 1;

		foreach ($data as $el) {
			$el->{static::MY_ORDER_COLUMN} = $order_val;
			$el->saveOrUpdate();
			$order_val++;
		}

	}

	private function exchange_order($el1,$el2) {
		$tmp1 = $el1->{static::MY_ORDER_COLUMN};
		$tmp2 = $el2->{static::MY_ORDER_COLUMN};

		$el1->order_val = $tmp2;
		$el1->saveOrUpdate();

		$el2->order_val = $tmp1;
		$el2->saveOrUpdate();
	}

	private function findPreviousElement() {

		$element_type = $this->my_element->element_type;
		$element_id = $this->my_element->element_id;

		$order_val = $this->my_element->{static::MY_ORDER_COLUMN};

		$do = new AttachmentInElementDO();

		$db = db();

		$cond = _and(_lt(static::MY_ORDER_COLUMN,$order_val));

		foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
			$cond->add(_eq($col_name,$this->my_element->{$col_name}));
		}

		$previous = $do->findFirst($cond)->orderBy(desc(static::MY_ORDER_COLUMN))->go($db);

		return $previous;
	}

	private function findNextElement() {
		
		$element_type = $this->my_element->element_type;
		$element_id = $this->my_element->element_id;

		$order_val = $this->my_element->{static::MY_ORDER_COLUMN};

		$do = new AttachmentInElementDO();

		$db = db();

		$cond = _and(_gt(static::MY_ORDER_COLUMN,$order_val));

		foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
			$cond->add(_eq($col_name,$this->my_element->{$col_name}));
		}

		$next = $do->findFirst($cond)->orderBy(asc(static::MY_ORDER_COLUMN))->go($db);

		return $next;
	}

	public function move_to_previous() {

		$this->checkRequiredOrderingConstants();

		$previous = $this->findPreviousElement();

		if ($previous) {
			$this->exchange_order($previous,$this->my_element);
		}

	}

	public function move_to_next() {

		$this->checkRequiredOrderingConstants();
		
		$next = $this->findNextElement();

		if ($next) {
			$this->exchange_order($this->my_element,$next);
		}

	}

	public function move_to_first() {

		$this->checkRequiredOrderingConstants();

		$clazz = get_class($this->my_element);

		$do = new $clazz();

		$db = db();

		$cond = _and(_not_in('id',[$this->my_element->id]));

		foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
			$cond->add(_eq($col_name,$this->my_element->{$col_name}));
		}

		$all_elements = $do->findAll($cond)->orderBy(asc(static::MY_ORDER_COLUMN))->go($db);

		$final_array = [$this->my_element];

		foreach ($all_elements as $other_el) {
			$final_array [] = $other_el;
		}

		$this->reorder_all($final_array);
	}

	public function move_to_last() {

		$this->checkRequiredOrderingConstants();

		$clazz = get_class($this->my_element);

		$do = new $clazz();

		$db = db();

		$cond = _and(_not_in('id',[$this->my_element->id]));

		foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
			$cond->add(_eq($col_name,$this->my_element->{$col_name}));
		}

		$all_elements = $do->findAll($cond)->orderBy(asc(static::MY_ORDER_COLUMN))->go($db);

		$final_array = [];

		foreach ($all_elements as $other_el) {
			$final_array [] = $other_el;
		}

		$final_array[] = $this->my_element;

		$this->reorder_all($final_array);

	}

}