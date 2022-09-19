<?php



abstract class LAbstractDataObject {
	
	private $__columns = null;

	private static $__reflection_class = null;

	const __LAST_AFFECTED_ROWS_IS_INSERT = 1;

	const ID_COLUMN = "id";
	const TABLE = null;
	
	const MY_DB = null;

	private static $__my_db = null;

	private static $__distinct_option = false;
	private static $__conditions = null;
	private static $__order_by = null;
	
	private static $__page_size = null;
	private static $__page_number = null;

	private static $__search_mode = null;

	function __construct($pk = null,$db = null) {

		if ($pk!=null) {
			$this->loadFromPk($pk,$db);
		}
	}

	private static function db($db = null) {
		if ($db) {
			self::$__my_db = LDbConnectionManager::get($db);
			return self::class;
		}
		if (static::MY_DB) {
			self::$__my_db = LDbConnectionManager::get(static::MY_DB);
			return self::class;
		}

		self::$__my_db = LDbConnectionManager::getLastConnectionUsed();
		return static::class;

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

		$id_column = static::ID_COLUMN;

		$fields = self::$__search_mode == 'count' ? 'count(*) AS C' : '*';

		$s = select($fields,$table);

		if (self::$__distinct_option) $s = $s->with_distinct();

		if (self::$__conditions) $s = $s->where(... self::$__conditions);

		if (self::$__search_mode != 'count') {
			if (self::$__order_by) $s = $s->order_by(... self::$__order_by);

			if (self::$__page_size && self::$__page_number) $s = $s->paginate(self::$__page_size,self::$__page_number);
		}

		$result = self::processSearchResults($s->go(self::$__my_db));

		self::resetSearch();

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
			throw new \Exception("No property with name ".$name." is found in data object of class ".static::class);
		}
	}

	private static function isInternalPrivateProperty($name) {
		return strpos($name,'__')===0;
	}

	private static function getTable() {
		$table = static::TABLE;

		if (!$table) throw new \Exception("table constant in data object has not been defined!");

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

	private function setAllColumnsData($data) {

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

		$id_column = static::ID_COLUMN;

		$result = select('*',$table)->where(_eq($id_column,$pk))->go(self::$__my_db);

		if (count($result)==0) return false;

		if (count($result)>1) throw new \Exception("Found more than one entry for primary key ".$pk." in data object ".static::class);

		$row = $result[0];

		$this->setAllColumnsData($row);

		return true;
	}

	public function delete($db=null) {

		self::db($db);

		$table = self::getTable();

		$id_column = static::ID_COLUMN;

		$id_value = $this->getColumnValue($id_column);

		if ($id_value == null) throw new \Exception("Can't delete a data object with no id yet. You need to save or load it before it can be deleted from database.");

		delete($table,[$id_column => $id_value])->go(self::$__my_db);

		return last_affected_rows()->go($db);
	}

	public function saveOrUpdate($db=null) {

		self::db($db);

		$table = self::getTable();

		$all_columns_data = $this->getAllColumnsData();

		$last_insert_id = insert($table)->column_list(array_keys($all_columns_data))->data(array_values($all_columns_data))->on_duplicate_key_update($all_columns_data)->go(self::$__my_db);

		$num_rows = last_affected_rows()->go(self::$__my_db);

		if ($num_rows == self::__LAST_AFFECTED_ROWS_IS_INSERT) {

			$this->setColumnValue(self::ID_COLUMN,$last_insert_id);

			return $last_insert_id;

		} else {
			return $this->getField(static::ID_COLUMN);
		}

		
	}

	public function __set($name,$value) {
		if ($this->usesAutoColumns()) {

			$this->__columns[$name] = $value;
		} else {
			throw new \Exception("Writing to unknown column : ".$name.". Declare it inside data object or remove all to use automatic mode.");
		}
	}

	public function __isset($name) {
		if ($this->usesAutoColumns()) {

			return isset($this->__columns[$name]);
		} else {
			 throw new \Exception("No column found with name ".$name." into this object.");
		}

	}

	public function __get($name) {
		if ($this->usesAutoColumns()) {
			if (isset($this->__columns[$name]))
				return $this->__columns[$name];
			else return null;
		} else {
			throw new \Exception("No column with name ".$name." found in this data object.");
		}
	}

	public function __toString() {

		$columns_data = $this->getAllColumnsData();

		$fields_list = [];
		foreach ($columns_data as $col_key => $col_data) {

			$col_string = is_string($col_data) ? "'".$col_data."'" : $col_data;

			$fields_list[] = "'".$col_key."' = ".$col_string;
		}
		

		return '[ '.implode(' , ',$fields_list).' ]';
	}

}