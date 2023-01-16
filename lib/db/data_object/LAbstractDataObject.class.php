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

	private $__columns = null;

	private static $__reflection_class = null;

	const __LAST_AFFECTED_ROWS_IS_INSERT = 1;

	const ID_COLUMN_NAME = "id";

	const MY_TABLE = null;
	
	const MY_CONNECTION = null;

	const HAS_STANDARD_OPERATIONS_COLUMNS = false;

	const VIRTUAL_COLUMNS_LIST = [];

	private $__virtual_columns = [];

	private static $__my_connection = null;

	private static $__distinct_option = false;
	private static $__conditions = null;
	private static $__order_by = null;
	
	private static $__page_size = null;
	private static $__page_number = null;

	private static $__search_mode = null;

	public static function hasStandardOperationsColumns() {
		return static::HAS_STANDARD_OPERATIONS_COLUMNS;
	}

	function __construct($pk = null,$db = null) {

		//col valore 0 non carica nulla, ok
		if ($pk!=null) {
			$this->loadFromPk($pk,$db);
		} else {

			$this->{static::ID_COLUMN_NAME} = 0;

			if (static::hasStandardOperationsColumns()) {
				$this->created_by();
			}
		}
	}

	private static function db($db = null) {
		if ($db) {
			self::$__my_connection = LDbConnectionManager::get($db);
			return self::class;
		}
		if (static::MY_CONNECTION) {
			self::$__my_connection = LDbConnectionManager::get(static::MY_CONNECTION);
			return self::class;
		}

		self::$__my_connection = LDbConnectionManager::getLastConnectionUsed();
		return static::class;

	}

	private static function getLastConnectionUsed() {
		return self::$__my_connection;
	}

	private static function resetSearch() {

		self::$__search_mode = null;

		self::$__distinct_option = false;
		self::$__conditions = null;
		self::$__order_by = null;
		self::$__page_size = null;
		self::$__page_number = null;

	}

	public static function go($db = null) {

		if (!self::$__search_mode) throw new \Exception("Search not correctly specified!");

		self::db($db);

		$table = self::getTable();

		$id_column = static::ID_COLUMN_NAME;

		$fields = self::$__search_mode == 'count' ? 'count(*) AS C' : '*';

		$s = select($fields,$table);

		if (self::$__distinct_option) $s = $s->with_distinct();

		if (static::hasStandardOperationsColumns()) {
			switch (self::$__soft_deleted_filter) {
				case self::SOFT_DELETED_FILTER_DEFAULT : {
					if (!self::$__conditions) {
						self::$__conditions = [_is_null(static::COLUMN_DELETED_AT)];
					} else {
						$cond = self::$__conditions;

						self::$__conditions = [_and(_and($cond),_is_null(static::COLUMN_DELETED_AT))];
					}
					break;
				}
				case self::SOFT_DELETED_FILTER_WITH : {

					//nothing to do

					break;
				}
				case self::SOFT_DELETED_FILTER_ONLY : {
					if (!self::$__conditions) {
						self::$__conditions = [_is_not_null(static::COLUMN_DELETED_AT)];
					} else {
						$cond = self::$__conditions;

						self::$__conditions = [_and(_and($cond),_is_not_null(static::COLUMN_DELETED_AT))];
					}
					break;
				}
				default : throw new \Exception("Illegal state exception during setup of filters for soft delete items.");

			}
		}

		if (self::$__conditions) $s = $s->where(... self::$__conditions);

		if (self::$__search_mode != 'count') {
			if (self::$__order_by) $s = $s->order_by(... self::$__order_by);

			if (self::$__page_size && self::$__page_number) $s = $s->paginate(self::$__page_size,self::$__page_number);
		}

		$result = self::processSearchResults($s->go(self::$__my_connection));

		self::resetSearch();

		return $result;
	}

	public function navigateFromColumn($column_name,$class) {

		$column_value = $this->getColumnValue($column_name);

		$result = new $class($column_value);

		return $result;

	}

	public function navigateFromOtherTableColumn($column_name,$class) {

		$id_value = $this->getColumnValue(self::ID_COLUMN_NAME);

		$result = $class::findAll(_eq($column_name,$id_value))::go();

		return $result;

	}

	private static function processSearchResults(array $query_results) {

		$count = count($query_results);

		if (self::$__search_mode == 'count') {
			if ($count!=1) throw new \Exception("Unable to count rows for data object ".static::class);

			return $query_results[0]['C'];
		}

		if (self::$__search_mode == 'one') {
			if ($count!=1) throw new \Exception("Unable to find exactly one result : ".$count." results found.");

			$result = new static();

			$result->setAllColumnsData($query_results[0]);

			return $result;
		}

		if (self::$__search_mode == 'first') {
			if ($count == 0) throw new \Exception("Unable to find at least one row for search results.");

			$result = new static();

			$result->setAllColumnsData($query_results[0]);

			return $result;
		}

		if (self::$__search_mode == 'all') {

			$result = new LDataObjectCollection();

			foreach ($query_results as $row) {
				$obj = new static();
				$obj->setAllColumnsData($row);
				$result->add($obj);
			}

			$result->setCollectionClass(static::class);

			return $result;
		}

		throw new \Exception("Unable to find valid search mode to process : ".self::$__search_mode);

	}

	public static function count(... $conditions) {
		self::$__search_mode = "count";

		if (!empty($conditions)) {
			self::$__conditions = $conditions;
		}

		return static::class;
	}

	public static function findFirst(... $conditions) {
		
		self::$__search_mode = "first";

		if (!empty($conditions)) {
			self::$__conditions = $conditions;
		}

		return static::class;

	}

	public static function findOne(... $conditions) {

		self::$__search_mode = "one";

		if (!empty($conditions)) {
			self::$__conditions = $conditions;
		}

		return static::class;
	}

	public static function findAll(... $conditions) {

		self::$__search_mode = "all";
	
		if (!empty($conditions)) {
			self::$__conditions = $conditions;
		}

		return static::class;
	}

	public static function distict() {

		self::$__distinct_option = true;

		return static::class;
	}

	public static function orderBy(... $order_by_elements) {

		self::$__order_by = $order_by_elements;

		return static::class;

	}

	public static function paginate($page_size,$page_number) {

		self::$__page_size = $page_size;

		self::$__page_number = $page_number;

		return static::class;

	}

	private static function initializeReflectionClass() {
		if (self::$__reflection_class == null)
			self::$__reflection_class = new ReflectionClass(static::class);
	}

	private static function getObjectProperty($name) {
		
		try {

			$p = self::$__reflection_class->getProperty($name);

			return $p;
		}
		catch (ReflectionException $ex) {
			throw new \Exception("No property with name '".$name."' is found in data object of class ".static::class);
		}
	}

	public static function getMethod($name) {

		try {

			$p = self::$__reflection_class->getMethod($name);

			return $p;

		} catch (ReflectionException $ex) {
			throw new \Exception("No method with name '".$name."' is found in data object of class ".static::class);
		}


	}

	private static function isInternalPrivateProperty($name) {
		return strpos($name,'__')===0;
	}

	private static function getTable() {
		$table = static::MY_TABLE;

		if (!$table) throw new \Exception("TABLE constant in data object has not been defined!");

		return $table;
	}

	private function getColumnValue($name) {
		if ($this->usesAutoColumns()) {
			if (isset($this->__columns[$name]))
				return $this->__columns[$name];
			else return null;
		} else {
			self::initializeReflectionClass();

			$p = self::getObjectProperty($name);

			return $p->getValue($this);
		}
	}


	private function setColumnValue($name,$value) {
		if ($this->usesAutoColumns()) {
			$this->__columns[$name] = $value;
		} else {
			
			self::initializeReflectionClass();

			$p = self::getObjectProperty($name);

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
				$result[$key] = $value;
			}
		}

		return $result;

	}

	private function setAllColumnsData($data) {

		$data = $this->normalizeData($data);

		if ($this->usesAutoColumns()) $this->__columns = $data;
		else {
			self::initializeReflectionClass();

			foreach ($data as $name => $value) {

				if (self::isInternalPrivateProperty($name)) continue;

				$p = self::getObjectProperty($name);

				$p->setValue($this,$value);
			}
		}
	}

	private function getAllColumnsData() {

		if ($this->usesAutoColumns()) return $this->__columns;
		else {
			$object_vars = get_object_vars($this);

			$result = [];

			foreach ($object_vars as $name => $value) {
				if (self::isInternalPrivateProperty($name)) continue;

				$v = $name;

				$result[$name] = $value;
			}

			return $result;
		}

	}

	private function usesAutoColumns() {
		if ($this->__columns != null) return true;

		$object_vars = get_object_vars($this);

		foreach ($object_vars as $name => $value) {
			if (self::isInternalPrivateProperty($name)) continue;

			return false;
		}

		$this->__columns = array();

		return true;
	}

	private function loadFromPk($pk,$db=null) {

		self::db($db);

		$table = self::getTable();

		$id_column = static::ID_COLUMN_NAME;

		$result = select('*',$table)->where(_eq($id_column,$pk))->go(self::$__my_connection);

		if (count($result)==0) return false;

		if (count($result)>1) throw new \Exception("Found more than one entry for primary key ".$pk." in data object ".static::class);

		$row = $result[0];

		$this->setAllColumnsData($row);

		return true;
	}

	public function hard_delete($db=null) {

		self::db($db);

		$table = self::getTable();

		$id_column = static::ID_COLUMN_NAME;

		$id_value = $this->getColumnValue($id_column);

		if ($id_value == null) throw new \Exception("Can't delete a data object with no id yet. You need to save or load it before it can be deleted from database.");

		delete($table,[$id_column => $id_value])->go(self::$__my_connection);

		return last_affected_rows()->go($db);
	}

	public function delete($db=null) {
		if (static::hasStandardOperationsColumns())
			$this->soft_delete(true,$db);
		else
			$this->hard_delete($db);
	}

	public function saveOrUpdate($db=null) {

		self::db($db);

		$table = self::getTable();

		$all_columns_data = $this->getAllColumnsData();

		$no_id_columns_data = $all_columns_data;
		unset($no_id_columns_data[static::ID_COLUMN_NAME]);

		if ($all_columns_data[static::ID_COLUMN_NAME]==0) {
			$all_columns_data = $no_id_columns_data;	
		}

		$last_insert_id = insert($table)->column_list(array_keys($all_columns_data))->data(array_values($all_columns_data))->on_duplicate_key_update($no_id_columns_data)->go(self::$__my_connection);


		$num_rows = last_affected_rows()->go(self::$__my_connection);

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

	private static $__soft_deleted_filter = "default";

	public static function with_soft_deleted() {
		
		self::checkSoftColumns();

		self::$__soft_deleted_filter = "with";

		return static::class;
	}


	public static function only_soft_deleted() {
		
		self::checkSoftColumns();

		self::$__soft_deleted_filter = "only";

		return static::class;
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

}