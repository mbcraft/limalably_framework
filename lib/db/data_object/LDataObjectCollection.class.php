<?php


class LDataObjectCollection implements ArrayAccess {

	private $connection_name = null;

	private $where_clause = null;

	private $common_data_object_class = null;

	private $common_changes = null;

	private $collection = array();

	private $data_objects_loaded = false;

	private $bulk_mode = false;

	private $executed = true;

	public function __loadDataObjects() {
		if ($this->data_objects_loaded) return;

		//implement ...

		$this->data_objects_loaded = true;
	}

	public function __describeUniformObjects($common_data_object_class,$where_clause,$connection_name) {

		if ($this->common_data_object_class!=null) throw new \Exception("Data objects are already described, can't describe more!");

		$this->common_data_object_class = $common_data_object_class;
		$this->where_clause = $where_clause;
		$this->connection_name = $connection_name;

		if (!$common_data_object_class::getInstanceCreationStrategy() instanceof LSingleRowSingleClassDOCreationStrategy) {
			$this->bulk_mode = true;
		} else {
			$this->bulk_mode = false;
		}
	}

	private function checkNotAlreadyExecuted() {
		if ($this->executed) throw new \Exception("Operation already executed, use a new collection for doing a new operation.");
	}

	/**
	 * 2 tipologie di count :
	 * 
	 * - conteggio effettivo degli oggetti caricati
	 * - conteggio con query usando il criterio della find
	*/
	public function count() {
		$this->checkNotAlreadyExecuted();

		if ($this->data_objects_loaded) {
			$c = count($this->collection);

			$this->executed = true;

			return $c;
		} else {
			if (!$this->connection_name) throw new \Exception("Objects are not described yet!");

			$db = LDbConnectionManager::get($this->connection_name);

			$table = $this->common_data_object_class::TABLE;

			$result = select('count(*) AS C',$table,$this->where_clause)->go($db);

			$c = $result[0]['C'];

			$this->executed = true;

			return $c;
		}
	}

	/**
	Vari tipi di saveOrUpdate :
	- replace con where a criterio secca in base alla find effettuata (1 query) x cambiamenti massivi
	- replace delegata all'oggetto (implementarla)

	*/
	public function saveOrUpdateAll() {
		$this->checkNotAlreadyExecuted();
		//può essere fatto con una replace massiva oppure con una serie di replace singole in base alla strategia
		if ($this->bulk_mode) {
			$db = LDbConnectionManager::get($this->connection_name);

			$table = $this->common_data_object_class::TABLE;

			update($table,$this->changes,$this->where_clause)->go($db);

			$this->executed = true;
		}

		throw new \Exception("Not implemented yet!");
	}

	/**
	Vari tipi di delete :
	- delete con where a criterio secca in base alla find effettuata (1 query)
	- delete con where che usa la in sulle chiavi da eliminare in base alla colonna della chiave (1 query)
	- delete delegata all'oggetto (in caso di oggetti complessi, implementarla)
	*/
	public function deleteAll() {
		$this->checkNotAlreadyExecuted();

		if ($this->bulk_mode) {
			$db = LDbConnectionManager::get($this->connection_name);

			$table = $this->common_data_object_class::TABLE;

			delete($table,$this->where_clause)->go($db);

			$this->executed = true;
		} else {

		}
		
	}


	/**
	 * ArrayAccess interface
	*/
	public function offsetExists($offset) {
		$this->__loadDataObjects();

		return isset($this->collection[$offset]);
	}

	/**
	 * ArrayAccess interface
	*/
	public function offsetGet($offset) {
		$this->__loadDataObjects();

		return isset($this->collection[$offset]) : $this->collection[$offset] : null;
	}

	/**
	 * ArrayAccess interface
	*/
	public function offsetSet($offset,$value) {
		$this->where_clause = null;
		$this->bulk_mode = false;

		if (is_null($offset)) {
			$this->collection[] = $value;
		} else {
			$this->collection[$offset] = $value;
		}
	}

	/**
	 * ArrayAccess interface
	*/
	public function offsetUnset($offset) {
		$this->where_clause = null;
		$this->bulk_mode = false;

		unset($this->collection[$offset]);
	}


}