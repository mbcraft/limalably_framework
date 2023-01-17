<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LDataObjectCollection implements ArrayAccess,Countable,Iterator {

	private $collection = array();

	private $collection_class = null;

	private $position = 0;

	public function current() {
		return $this->collection[$this->position];
	}

	public function key() {
		return $this->position;
	}

	public function next() {
		$this->position++;
	}

	public function rewind() {
		$this->position = 0;
	}

	public function valid() {
		return $this->position<count($this->collection);
	}


	public function setCollectionClass($clazz) {
		$this->collection_class = $clazz;
	}

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

		return isset($this->collection[$offset]);
	}

	/**
	 * ArrayAccess interface
	*/
	public function offsetGet($offset) {

		return isset($this->collection[$offset]) ? $this->collection[$offset] : null;
	}

	/**
	 * ArrayAccess interface
	*/
	public function offsetSet($offset,$value) {

		if (is_null($value)) throw new \Exception("Unable to add null to this collection");

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

		unset($this->collection[$offset]);
	}

	function __call($method_name,$arguments) {
		
		if (count($this->collection)>0) {

			$r = new ReflectionClass($this->collection_class);

			$m = $r->getMethod($method_name);

			foreach ($this->collection as $elem) {
				$m->invoke($elem,$arguments);
			}
		}

		return true;

	}

	function __toString() {

		$result = "[\n";

		$obj_list = [];

		foreach ($this->collection as $obj) {
			$obj_list []= "\t".$obj;
		}

		$result .= implode(" ,\n",$obj_list);
		$result .= "\n]";

		return $result;
	}

}