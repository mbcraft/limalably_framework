<?php


class LDataObjectCollection implements ArrayAccess,Countable {

	private $collection = array();

	/**
	Vari tipi di saveOrUpdate :
	- replace con where a criterio secca in base alla find effettuata (1 query) x cambiamenti massivi
	- replace delegata all'oggetto (implementarla)

	*/
	public function saveOrUpdateAll() {
	
		foreach ($this->collection as $element) {
			$element->saveOrUpdate();
		}
	}

	/**
	Vari tipi di delete :
	- delete con where a criterio secca in base alla find effettuata (1 query)
	- delete con where che usa la in sulle chiavi da eliminare in base alla colonna della chiave (1 query)
	- delete delegata all'oggetto (in caso di oggetti complessi, implementarla)
	*/
	public function deleteAll() {

		foreach ($this->collection as $element) {
			$element->delete();
		}
	}

	public function add($object) {
		if (is_null($object)) throw new \Exception("Unable to add null to this collection");

		$this->collection[] = $object;
		
	}

	/**
	 * Countable interface
	 */
	public function count() {
		return count($this->collection);
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

		return isset($this->collection[$offset]) ? $this->collection[$offset] : null;
	}

	/**
	 * ArrayAccess interface
	*/
	public function offsetSet($offset,$value) {

		if (is_null($value)) throw new \Exception("Unable to add null to this collection");

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