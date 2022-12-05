<?php


class LTagReference implements LIParentable {
	
	private $my_parent = null;
	private $ref_child_name;

	function __construct($ref_child_name) {
		$this->ref_child_name = $ref_child_name;
	}

    public function setParent($parent) {
        $this->my_parent = $parent;
    }

    public function getParent() {
        return $this->my_parent;
    }

    private function realElementName($name) {
        $step1 = str_replace('__','-',$name);
        $step2 = str_replace('§','__',$step1);

        return $step2;
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
				$result .= $element;
			}
		} else {
			$result .= $element_or_list;
		}

		return $result;
	}

}