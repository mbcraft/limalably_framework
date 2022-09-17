<?php



abstract class LAbstractDataObject {
	
	private $__columns = null;

	private static $__reflection_class = null;

	const __LAST_AFFECTED_ROWS_IS_INSERT = 1;

	const ID_COLUMN = "id";
	const TABLE = null;
	
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

	public function delete($db) {

		$table = self::getTable();

		$id_column = static::ID_COLUMN;

		$id_value = $this->getColumnValue($id_column);

		if ($id_value == null) throw new \Exception("Can't delete a data object with no id yet. You need to save or load it before it can be deleted from database.");

		delete($table,[$id_column => $id_value])->go($db);

		return last_affected_rows()->go($db);
	}

	public function saveOrUpdate($db) {

		$table = self::getTable();

		$all_columns_data = $this->getAllColumnsData();

		$last_insert_id = insert($table)->column_list(array_keys($all_columns_data))->data(array_values($all_columns_data))->on_duplicate_key_update($all_columns_data)->go($db);

		$num_rows = last_affected_rows()->go($db);

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



	function getInstanceCreationStrategy() {
		return new LSingleRowSingleClassDOCreationStrategy(static::class);
	}

}