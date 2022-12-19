<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTagReference implements LIParentable {
	
	private $my_parent = null;
	private $ref_child_name;

	private $override_attributes = [];

	function __construct($ref_child_name) {
		$this->ref_child_name = $ref_child_name;
	}

    public function setParent($parent) {
        $this->my_parent = $parent;
    }

    public function getParent() {
        return $this->my_parent;
    }

	/**
    Non è detto che in futuro non crei semplicemente una tabella di nomi di elementi di cui fare il replace in toto
    in modo da usare comunque il singolo underscore per il trattino medio (-).
    Comunque anche questa non è male come soluzione. Non è detto che non si possano usare entrambe.
    */
    private function realElementName($name) {
        $step1 = str_replace('__','-',$name);
        $step2 = str_replace('§','__',$step1);

        return $step2;
    }
    
    //attributes management

    function __call($method_name,$parameters) {

        if ($this->tag_mode == self::TAG_MODE_AUTO) throw new \Exception("Mode is not correctly setup!");

        if (count($parameters)==0) {
            $this->setAttribute($method_name,false);
            return $this;
        }

        if ($method_name=='class' || $method_name=='style') {

            if ($method_name=='class')
                $char = ' ';
            if ($method_name=='style')
                $char = ';';

            foreach ($parameters as $p)
            {
                $this->pushCharSeparatedAttributeValue($method_name,$char,$p);
            }

            return $this;
        } else {
            if (count($parameters)!=1) throw new \Exception("Invalid number of values for attribute ".$method_name);

            $this->setAttribute($method_name,$parameters[0]);
        
            return $this;
        }

    }

    function __set($key,$value) {
        throw new \Exception("Do not use this syntax. It is not supported.");
    }

    function __get($key) {
        throw new \Exception("Do not use this syntax. It is not supported.");
    }

    function __isset($key) {

        $real_key = $this->realElementName($key);

        if (isset($this->override_attributes[$real_key])) return true;

        return false;
    }

    function __unset($key) {

        $real_key = $this->realElementName($key);

        if (isset($this->override_attributes[$real_key])) {
            unset($this->override_attributes[$real_key]);
        }        

    }

    function setAttribute($key,$value)
    {
        $this->override_attributes[$this->realElementName($key)] = $value;
    }

    function getAttribute($key) {

        $key = $this->realElementName($key);

        return isset($this->override_attributes[$key]) ? $this->override_attributes[$key] : null;
    }

    function pushCharSeparatedAttributeValue($key,$char,$value) {
        if (strlen($char)!=1) throw new \Exception("Separator is not a single char. Found : ".$char);

        $current_value = $this->getAttribute($key);

        if (!$current_value) {
            $current_value = $value;
        }
        else {
            $current_value .= $value;
        }

        if (!LStringUtils::endsWith($current_value,$char)) $current_value .= $char;

        $this->setAttribute($key,$current_value);
    }

    function hasAttribute($key)
    {
        return isset($this->override_attributes[$this->realElementName($key)]);
    }

    private function applyOverrideAttributes($tag) {
    	foreach ($this->override_attributes as $key => $value) {
    		$tag->setAttribute($key,$value);
    	}
    }

    public function findAncestorChildByName($child_name) {

        $child_name = $this->realElementName($child_name);

        return $this->my_parent->findAncestorChildByName($child_name);
    }

	function __toString() {
		$element_or_list = $this->findAncestorChildByName($this->ref_child_name);

		$result = "";

		if (is_array($element_or_list)) {
			foreach ($element_or_list as $element) {
				if ($element instanceof LTag) {
					$element = $element->makeClone();
					$this->applyOverrideAttributes($element);
				}
				$result .= $element;
			}
		} else {
			if ($element_or_list instanceof LTag) {
				$element_or_list = $element_or_list->makeClone();
				$this->applyOverrideAttributes($element_or_list);
			}
			$result .= $element_or_list;
		}

		return $result;
	}

}