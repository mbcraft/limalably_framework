<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

abstract class LAbstractDataObject implements LIStandardOperationsColumnConstants {
	
	const SOFT_DELETED_FILTER_DEFAULT = "default";
	const SOFT_DELETED_FILTER_WITH = "with";
	const SOFT_DELETED_FILTER_ONLY = "only";

	const SESSION_USER_ID_PATH = "/user/id";

	private $__columns = null; //no need special clone behaviour
	private $__virtual_columns = []; //no need special clone behaviour

	private $__reflection_class = null; //no need special clone behaviour

	private $__my_connection_name = null;
	private $__my_connection = null; //

	public $__distinct_option = false;
	public $__conditions = null; //needs special clone behaviour
	public $__order_by = null; //needs special clone behaviour
	
	public $__page_size = null; //no need
	public $__page_number = null; //no need

	public $__search_mode = null; //no need

	private static $__lastOrderingCache = [];

	const __LAST_AFFECTED_ROWS_IS_INSERT = 1;

	const ID_COLUMN_NAME = "id";

	const MY_TABLE = null;
	
	const MY_CONNECTION = null;

	const MY_ORDER_COLUMN = null;

	const MY_ORDER_GROUP_COLUMNS = null;

	const HAS_STANDARD_OPERATIONS_COLUMNS = false;

	const VIRTUAL_COLUMNS_LIST = [];

	const MY_JSON_COLUMNS = [];

	public static function hasStandardOperationsColumns() {
		return static::HAS_STANDARD_OPERATIONS_COLUMNS;
	}

	function __clone() {
		if ($this->__my_connection_name) {
			$this->__my_connection = LDbConnectionManager::get($this->__my_connection_name);
		}
	}

	function __construct($pk = null,$db = null) {

		//col valore 0 non carica nulla, ok
		if ($pk!=null) {
			$result = $this->loadFromPk($pk,$db);
			if (!$result) throw new \Exception("Unable to find object of class ".static::class." with pk = ".$pk);
		} else {

			$this->{static::ID_COLUMN_NAME} = 0;

			if (static::hasStandardOperationsColumns()) {
				$this->created_by();
			}
		}
	}

	public function isNew() {
		return $this->{static::ID_COLUMN_NAME} == 0;
	}

	private function db($db = null) {
		if ($db) {
			$this->__my_connection = LDbConnectionManager::get($db);
			$this->__my_connection_name = $this->__my_connection->getName();
			return $this;
		}
		if (static::MY_CONNECTION) {
			$this->__my_connection = LDbConnectionManager::get(static::MY_CONNECTION);
			$this->__my_connection_name = $this->__my_connection->getName();
			return $this;
		}

		$this->__my_connection = LDbConnectionManager::getLastConnectionUsed();
		$this->__my_connection_name = $this->__my_connection->getName();
		return $this;

	}

	private function getLastConnectionUsed() {
		return $this->__my_connection;
	}

	private function resetSearch() {

		$this->__search_mode = null;

		$this->__distinct_option = false;
		$this->__conditions = null;
		$this->__order_by = null;
		$this->__page_size = null;
		$this->__page_number = null;

	}

	public function go($db = null) {

		if (!$this->__search_mode) throw new \Exception("Search not correctly specified!");

		$this->db($db);

		$table = $this->getTable();

		$id_column = static::ID_COLUMN_NAME;

		$fields = $this->__search_mode == 'count' ? 'count(*) AS C' : '*';

		$s = select($fields,$table);

		if ($this->__distinct_option) $s = $s->with_distinct();

		if (static::hasStandardOperationsColumns()) {
			switch ($this->__soft_deleted_filter) {
				case self::SOFT_DELETED_FILTER_DEFAULT : {
					if (!$this->__conditions) {
						$this->__conditions = [_is_null(static::COLUMN_DELETED_AT)];
					} else {
						$cond = $this->__conditions;

						$this->__conditions = [_and(_and($cond),_is_null(static::COLUMN_DELETED_AT))];
					}
					break;
				}
				case self::SOFT_DELETED_FILTER_WITH : {

					//nothing to do

					break;
				}
				case self::SOFT_DELETED_FILTER_ONLY : {
					if (!$this->__conditions) {
						$this->__conditions = [_is_not_null(static::COLUMN_DELETED_AT)];
					} else {
						$cond = $this->__conditions;

						$this->__conditions = [_and(_and($cond),_is_not_null(static::COLUMN_DELETED_AT))];
					}
					break;
				}
				default : throw new \Exception("Illegal state exception during setup of filters for soft delete items.");

			}
		}

		if ($this->__conditions) $s = $s->where(... $this->__conditions);

		if ($this->__search_mode != 'count') {
			if ($this->__order_by) $s = $s->order_by(... $this->__order_by);

			if ($this->__page_size && $this->__page_number) $s = $s->paginate($this->__page_size,$this->__page_number);
		}

		$result = $this->processSearchResults($s->go($this->__my_connection));

		$this->resetSearch();

		return $result;
	}

	public function navigateFromColumn($column_name,$class) {

		$column_value = $this->getColumnValue($column_name);

		$result = new $class($column_value);

		return $result;

	}

	public function navigateFromOtherTableColumn($column_name,$class) {

		$id_value = $this->getColumnValue(self::ID_COLUMN_NAME);

		$instance = new $class();

		$result = $instance->findAll(_eq($column_name,$id_value))->go();

		return $result;

	}

	private function processSearchResults(array $query_results) {

		$count = count($query_results);

		if ($this->__search_mode == 'count') {
			if ($count!=1) throw new \Exception("Unable to count rows for data object ".static::class);

			return $query_results[0]['C'];
		}

		if ($this->__search_mode == 'one') {
			if ($count!=1) throw new \Exception("Unable to find exactly one result : ".$count." results found.");

			$result = new static();

			$result->setAllColumnsData($query_results[0]);

			return $result;
		}

		if ($this->__search_mode == 'first') {
			if ($count == 0) throw new \Exception("Unable to find at least one row for search results.");

			$result = new static();

			$result->setAllColumnsData($query_results[0]);

			return $result;
		}

		if ($this->__search_mode == 'first_or_null') {
			if ($count == 0) return null;

			$result = new static();

			$result->setAllColumnsData($query_results[0]);

			return $result;
		}

		if ($this->__search_mode == 'all') {

			$result = new LDataObjectCollection();

			foreach ($query_results as $row) {
				$obj = new static();
				$obj->setAllColumnsData($row);
				$result->add($obj);
			}

			$result->setCollectionClass(static::class);

			return $result;
		}

		throw new \Exception("Unable to find valid search mode to process : ".$this->__search_mode);

	}

	public function count(... $conditions) {
		$this->__search_mode = "count";

		if (!empty($conditions)) {
			$this->__conditions = $conditions;
		}

		return $this;
	}

	public function findFirst(... $conditions) {
		
		$this->__search_mode = "first";

		if (!empty($conditions)) {
			$this->__conditions = $conditions;
		}

		return $this;

	}

	public function findFirstOrNull(... $conditions) {
		
		$this->__search_mode = "first_or_null";

		if (!empty($conditions)) {
			$this->__conditions = $conditions;
		}

		return $this;

	}

	public function findOne(... $conditions) {

		$this->__search_mode = "one";

		if (!empty($conditions)) {
			$this->__conditions = $conditions;
		}

		return $this;
	}

	public function findAll(... $conditions) {

		$this->__search_mode = "all";
	
		if (!empty($conditions)) {
			$this->__conditions = $conditions;
		}

		return $this;
	}

	public function distict() {

		$this->__distinct_option = true;

		return $this;
	}

	public function orderBy(... $order_by_elements) {

		$this->__order_by = $order_by_elements;

		return $this;

	}

	public function paginate($page_size,$page_number) {

		$this->__page_size = $page_size;

		$this->__page_number = $page_number;

		return $this;

	}

	private function initializeReflectionClass() {
		if (!$this->__reflection_class)
			$this->__reflection_class = new ReflectionClass(static::class);
	}

	private function getObjectProperty($name) {
		
		try {

			$p = $this->__reflection_class->getProperty($name);

			return $p;
		}
		catch (ReflectionException $ex) {
			throw new \Exception("No property with name '".$name."' is found in data object of class ".$this->__reflection_class);
		}
	}

	public function getMethod($name) {

		try {

			$p = $this->__reflection_class->getMethod($name);

			return $p;

		} catch (ReflectionException $ex) {
			throw new \Exception("No method with name '".$name."' is found in data object of class ".static::class);
		}


	}

	private function isInternalPrivateProperty($name) {
		return strpos($name,'__')===0;
	}

	private function getTable() {
		$table = static::MY_TABLE;

		if (!$table) throw new \Exception("MY_TABLE constant in data object has not been defined!");

		return $table;
	}

	private function getColumnValue($name) {
		if ($this->usesAutoColumns()) {
			if (isset($this->__columns[$name]))
				return $this->__columns[$name];
			else return null;
		} else {
			$this->initializeReflectionClass();

			$p = $this->getObjectProperty($name);

			return $p->getValue($this);
		}
	}


	private function setColumnValue($name,$value) {
		if ($this->usesAutoColumns()) {
			$this->__columns[$name] = $value;
		} else {
			
			$this->initializeReflectionClass();

			$p = $this->getObjectProperty($name);

			$p->setValue($this,$value);
		}
	}

	private function normalizeData($data) {

		$result = [];

		foreach ($data as $key => $value) {
			if (is_numeric($value)) {
				if (is_float($value))
					$result[$key] = (float) $value;
				else
					$result[$key] = (int) $value;
			} else {
				if (in_array($key,static::MY_JSON_COLUMNS)) {
					$result[$key] = json_decode($value,true);
				} else {
					$result[$key] = $value;
				}
			}
		}

		return $result;

	}

	private function setAllColumnsData($data) {

		$data = $this->normalizeData($data);

		if ($this->usesAutoColumns()) $this->__columns = $data;
		else {
			$this->initializeReflectionClass();

			foreach ($data as $name => $value) {

				if ($this->isInternalPrivateProperty($name)) continue;

				$p = $this->getObjectProperty($name);

				$p->setValue($this,$value);
			}
		}
	}

	private function processJsonColumns($columns) {

		$result = [];

		foreach ($columns as $key => $value) {

			if (in_array($key,static::MY_JSON_COLUMNS)) 
			{
				if (!is_array($value)) throw new \Exception("Column ".$key." must be an array to become JSON!");
				$result[$key] = json_encode($value,true);
			} else {
				$result[$key] = $value;
			}
		}

		return $result;

	}

	private function getAllColumnsData() {

		if ($this->usesAutoColumns()) return $this->processJsonColumns($this->__columns);
		else {
			$object_vars = get_object_vars($this);

			$result = [];

			foreach ($object_vars as $name => $value) {
				if ($this->isInternalPrivateProperty($name)) continue;

				$v = $name;

				$result[$name] = $value;
			}

			return $this->processJsonColumns($result);
		}

	}

	private function usesAutoColumns() {
		if ($this->__columns != null) return true;

		$object_vars = get_object_vars($this);

		foreach ($object_vars as $name => $value) {
			if ($this->isInternalPrivateProperty($name)) continue;

			return false;
		}

		$this->__columns = array();

		return true;
	}

	public function reload() {
		return $this->loadFromPk($this->{static::ID_COLUMN_NAME});
	}

	public static function loadOrNull($pk,$db=null) {
		$do = new static();
		$result = $do->loadFromPk($pk,$db);
		if ($result) return $do;
		else return null;
	}

	public static function load($pk,$db=null) {
		$result = self::loadOrNull($pk,$db);
		if (!$result) throw new \Exception("Unable to find object of class ".static::class." with pk = ".$pk);
		return $result;
	}

	private function loadFromPk($pk,$db=null) {

		$this->db($db);

		$table = $this->getTable();

		$id_column = static::ID_COLUMN_NAME;

		$result = select('*',$table)->where(_eq($id_column,$pk))->go($this->__my_connection);

		if (count($result)==0) return false;

		if (count($result)>1) throw new \Exception("Found more than one entry for primary key ".$pk." in data object ".static::class);

		$row = $result[0];

		$this->setAllColumnsData($row);

		return true;
	}

	public function hard_delete($db=null) {

		$this->db($db);

		$table = $this->getTable();

		$id_column = static::ID_COLUMN_NAME;

		$id_value = $this->getColumnValue($id_column);

		if ($id_value == null) throw new \Exception("Can't delete a data object with no id yet. You need to save or load it before it can be deleted from database.");

		delete($table,_eq($id_column,$id_value))->go($this->__my_connection);

		return last_affected_rows()->go($this->__my_connection);
	}

	public function delete($db=null) {

		if (static::MY_ORDER_COLUMN!=null) {

			$clazz = static::class;

			$do = new $clazz();

			$cond = _and(_not_in(static::ID_COLUMN_NAME,[$this->{static::ID_COLUMN_NAME}]));

			$this->pushNotSoftDeletedCondition($cond);

			foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
				$cond->add(_eq_null($col_name,$this->{$col_name}));
			}

			$all_other_elements = $do->findAll($cond)->orderBy(asc(static::MY_ORDER_COLUMN))->go($db);

			$this->reorder_all($all_other_elements);

			$this->invalidateOrderColumnLastCache();			
		}

		if (static::hasStandardOperationsColumns())
			$this->soft_delete(true,$db);
		else
			$this->hard_delete($db);


	}

	public function saveOrUpdate($db=null) {

		$this->db($db);

		$table = $this->getTable();

		if ($this->{static::ID_COLUMN_NAME}==0 && static::MY_ORDER_COLUMN!=null) {
			$this->setupOrderColumnWithLastValue();

			$this->invalidateOrderColumnLastCache();
		}

		if ($this->{static::ID_COLUMN_NAME}==0 && static::hasStandardOperationsColumns() && !$this->created_at) {
			$this->created_by();
		}

		$all_columns_data = $this->getAllColumnsData();

		$no_id_columns_data = $all_columns_data;
		unset($no_id_columns_data[static::ID_COLUMN_NAME]);

		if ($all_columns_data[static::ID_COLUMN_NAME]==0) {
			$all_columns_data = $no_id_columns_data;

		}

		$last_insert_id = insert($table)->column_list(array_keys($all_columns_data))->data(array_values($all_columns_data))->on_duplicate_key_update($no_id_columns_data)->go($this->__my_connection);

		$num_rows = last_affected_rows()->go($this->__my_connection);

		if ($num_rows == self::__LAST_AFFECTED_ROWS_IS_INSERT) {

			$this->setColumnValue(self::ID_COLUMN_NAME,$last_insert_id);

			return $last_insert_id;

		} else {
			return $this->getColumnValue(static::ID_COLUMN_NAME);
		}

		
	}

	public function created_by($user_id=true) {

		$this->last_updated_by($user_id);

		self::checkSoftColumns();
		$user_id = $this->checkNumericUserId($user_id);

		$this->{self::COLUMN_CREATED_BY} = $user_id;
		$this->{self::COLUMN_CREATED_AT} = date('Y-m-d H:i:s');

		return $this;
	}

	public function last_updated_by($user_id=true) {

		self::checkSoftColumns();
		$user_id = $this->checkNumericUserId($user_id);

		$this->{self::COLUMN_LAST_UPDATED_BY} = $user_id;
		$this->{self::COLUMN_LAST_UPDATED_AT} = date('Y-m-d H:i:s');

		return $this;
	}

	public function soft_delete($user_id=true,$db=null) {

		self::checkSoftColumns();
		$user_id = $this->checkNumericUserId($user_id);

		$this->{self::COLUMN_DELETED_BY} = $user_id;
		$this->{self::COLUMN_DELETED_AT} = date('Y-m-d H:i:s');

		return $this->saveOrUpdate($db);

	}

	public function soft_undelete($db=null) {

		self::checkSoftColumns();

		$this->{static::COLUMN_DELETED_AT} = null;
		$this->{static::COLUMN_DELETED_BY} = null;

		return $this->saveOrUpdate($db);

	}

	private $__soft_deleted_filter = "default";

	public function with_soft_deleted() {
		
		self::checkSoftColumns();

		$this->__soft_deleted_filter = "with";

		return $this;
	}

	public function only_soft_deleted() {
		
		self::checkSoftColumns();

		$this->__soft_deleted_filter = "only";

		return $this;
	}

	private function checkNumericUserId($user_id) {

		if ($user_id===true) {
			$user_id = LSession::get(self::SESSION_USER_ID_PATH);
		}

		if ($user_id && !is_numeric($user_id)) throw new \Exception("The user id for a safe create update delete column is not numeric!");

		return $user_id;
	}

	private static function checkSoftColumns() {
		if (!static::hasStandardOperationsColumns()) throw new \Exception("Soft columns are not enabled in this data object!");
	}

	private function isVirtualColumn($name) {
		return in_array($name,static::VIRTUAL_COLUMNS_LIST);
	}

	public function __set($name,$value) {

		if ($this->isVirtualColumn($name)) {
			$this->__virtual_columns[$name] = $value;
		} else {

			if ($this->usesAutoColumns()) {

				$this->__columns[$name] = $value;
			} else {
				throw new \Exception("Writing to unknown column : ".$name.". Declare it inside data object or remove all to use automatic mode.");
			}
		}
	}

	public function __isset($name) {
		if ($this->isVirtualColumn($name)) {
			return isset($this->__virtual_columns[$name]);
		} else {
			if ($this->usesAutoColumns()) {

				return isset($this->__columns[$name]);
			} else {
				 throw new \Exception("No column found with name ".$name." into this object.");
			}
		}
	}

	public function __get($name) {
		if ($this->isVirtualColumn($name)) {
			return $this->__virtual_columns[$name];
		} else {
			if ($this->usesAutoColumns()) {
				if (isset($this->__columns[$name]))
					return $this->__columns[$name];
				else return null;
			} else {

				throw new \Exception("No column with name ".$name." found in this data object.");
			}
		}
	}

	public function __toString() {

		$columns_data = $this->getAllColumnsData();

		$fields_list = [];
		foreach ($columns_data as $col_key => $col_data) {

			if (is_array($col_data))
				$col_string = json_encode($col_data,true);
			else
				$col_string = is_string($col_data) ? "'".$col_data."'" : $col_data;

			$fields_list[] = "'".$col_key."' = ".$col_string;
		}

		$virtual_columns = $this->__virtual_columns;

		foreach ($virtual_columns as $col_key => $col_data) {

			$col_string = is_string($col_data) ? "'".$col_data."'" : $col_data;

			$field_list[] = "(".$col_key.") = ".$col_string;
		}
		

		return '[ '.implode(' , ',$fields_list).' ]';
	}

	private function pushNotSoftDeletedCondition($cond) {
		if (static::hasStandardOperationsColumns()) {
			$cond->add(_is_null('deleted_at'));
		}
	}

	private function getOrderColumnLastCacheName() {

		$name = static::class;

		foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
			
			$value = $this->{$col_name};

			if ($value===null) $name.= "_NULL";
			else $name .= "_".$value;
		}

		$name.= "_LAST";

		return $name;
	}

	private function getOrderColumnLast() {

		$last_cache_name = $this->getOrderColumnLastCacheName();

		if (isset(self::$__lastOrderingCache[$last_cache_name])) return self::$__lastOrderingCache[$last_cache_name];

		$value = $this->calculateOrderColumnLast();

		self::$__lastOrderingCache[$last_cache_name] = $value;

		return $value; 

	}

	private function invalidateOrderColumnLastCache() {

		$last_cache_name = $this->getOrderColumnLastCacheName();

		if (isset(self::$__lastOrderingCache[$last_cache_name])) unset(self::$__lastOrderingCache[$last_cache_name]);

	}

	private function calculateOrderColumnLast() {

		$clazz = static::class;

		$do = new $clazz();

		$db = db();

		$cond = _and();

		$this->pushNotSoftDeletedCondition($cond);

		foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
			$cond->add(_eq_null($col_name,$this->{$col_name}));
		}

		$total = $do->findAll($cond)->count()->go($db);

		return $total;

	}

	private function setupOrderColumnWithLastValue() {

		if ($this->{static::MY_ORDER_COLUMN}==null) {
		
			$total = $this->getOrderColumnLast();
		
			$this->{static::MY_ORDER_COLUMN} = $total + 1;
		}
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

		$order_val = $this->{static::MY_ORDER_COLUMN};

		$clazz = get_class($this);

		$do = new $clazz();

		$db = db();

		$cond = _and(_lt(static::MY_ORDER_COLUMN,$order_val));

		$this->pushNotSoftDeletedCondition($cond);

		foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
			$cond->add(_eq_null($col_name,$this->{$col_name}));
		}

		$previous = $do->findFirstOrNull($cond)->orderBy(desc(static::MY_ORDER_COLUMN))->go($db);

		return $previous;
	}

	private function findNextElement() {

		$order_val = $this->{static::MY_ORDER_COLUMN};

		$clazz = get_class($this);

		$do = new $clazz();

		$db = db();

		$cond = _and(_gt(static::MY_ORDER_COLUMN,$order_val));

		$this->pushNotSoftDeletedCondition($cond);

		foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
			$cond->add(_eq_null($col_name,$this->{$col_name}));
		}

		$next = $do->findFirstOrNull($cond)->orderBy(asc(static::MY_ORDER_COLUMN))->go($db);

		return $next;
	}

	public function move_to_previous() {

		$this->checkRequiredOrderingConstants();

		$previous = $this->findPreviousElement();

		if ($previous) {
			$this->exchange_order($previous,$this);
		}

	}

	public function move_to_next() {

		$this->checkRequiredOrderingConstants();
		
		$next = $this->findNextElement();

		if ($next) {
			$this->exchange_order($this,$next);
		}

	}

	public function move_to_first() {

		$this->checkRequiredOrderingConstants();

		$clazz = get_class($this);

		$do = new $clazz();

		$db = db();

		$cond = _and(_not_in('id',[$this->id]));

		$this->pushNotSoftDeletedCondition($cond);

		foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
			$cond->add(_eq_null($col_name,$this->{$col_name}));
		}

		$all_elements = $do->findAll($cond)->orderBy(asc(static::MY_ORDER_COLUMN))->go($db);

		$final_array = [$this];

		foreach ($all_elements as $other_el) {
			$final_array [] = $other_el;
		}

		$this->reorder_all($final_array);
	}

	public function move_to_last() {

		$this->checkRequiredOrderingConstants();

		$clazz = get_class($this);

		$do = new $clazz();

		$db = db();

		$cond = _and(_not_in('id',[$this->id]));

		$this->pushNotSoftDeletedCondition($cond);

		foreach (static::MY_ORDER_GROUP_COLUMNS as $col_name) {
			$cond->add(_eq_null($col_name,$this->{$col_name}));
		}

		$all_elements = $do->findAll($cond)->orderBy(asc(static::MY_ORDER_COLUMN))->go($db);

		$final_array = [];

		foreach ($all_elements as $other_el) {
			$final_array [] = $other_el;
		}

		$final_array[] = $this;

		$this->reorder_all($final_array);

	}

	public function is_first() {

		if (static::MY_ORDER_COLUMN)
			return $this->{static::MY_ORDER_COLUMN} == 1;
		else throw new \Exception("Can't use is_first() : MY_ORDER_COLUMN is not defined!");
	}

	public function is_last() {
		if (static::MY_ORDER_COLUMN)
			return $this->{static::MY_ORDER_COLUMN} == $this->getOrderColumnLast();
		else throw new \Exception("Can't use is_last() : MY_ORDER_COLUMN is not defined!");
	}

}