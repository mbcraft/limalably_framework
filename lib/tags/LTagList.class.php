<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTagList implements ArrayAccess, LITagRenderingTips {

	private $my_parent = null;
    private $children = array();

    private $indent_mode = self::TAG_INDENT_AUTO;

    public function setParent($parent) {
        $this->my_parent = $parent;
    }

    public function getParent() {
        return $this->my_parent;
    }

    function __call($method_name,$parameters) {
    	throw new \Exception("Do not use this syntax. It is not supported.");
    }
    
    function __set($key,$value) {
        throw new \Exception("Do not use this syntax. It is not supported.");
    }

    function __get($key) {
        throw new \Exception("Do not use this syntax. It is not supported.");
    }

    function __isset($key) {

        throw new \Exception("Do not use this syntax. It is not supported.");
    }

    function __unset($key) {

        throw new \Exception("Do not use this syntax. It is not supported.");

    }

    //child management

    private function parentedChild($child) {
    	if ($child instanceof LTagList) throw new \Exception("Creating list of lists of tag is not supported!");
        if ($child instanceof LTag || $child instanceof LTagReference) {
            $child->setParent($this);
        }

        return $child;
    } 

    public function add(... $child)
    {
        if ($this->tag_mode == self::TAG_MODE_AUTO) throw new \Exception("Mode is not correctly setup!");
        if ($this->tag_mode != self::TAG_MODE_OPEN_CONTENT_CLOSE) throw new \Exception("Tag mode not valid for add child : ".$this->getPrintableTagMode());

        foreach ($child as $c)
        {
            $this->setChild(null,$c);
        }

        return $this;
    }

    function getChildren()
    {
        return $this->children;
    }

    function getChild($child_name) {

        $child_name = $this->realElementName($child_name);

        if (isset($this->children[$$child_name])) return $this->children[$child_name];
        else {
            if (in_array($child_name,$this->required_children)) throw new \Exception("Missing children ".$child_name." in tag ".$this->original_tag_name);
            else return "<!-- empty child '".$child_name."' -->";
        }
    }

    function setChild($offset,$child) {
        if (is_null($child)) throw new \Exception("Unable to add null to this child list");

        if (is_null($offset)) {
            $this->children[] = $this->parentedChild($child);
        } else {
        	if (is_string($offset)) throw new \Exception("Named childs are not supported!");

            $this->children[$offset] = $this->parentedChild($child);
            
        }
    }

    function hasChild($child_name) {

        return isset($this->children[$child_name]);
    }

    public function findAncestorChildByName($child_name) {

        if ($parent==null) throw new \Exception("Missing unknown child ".$child_name);
        else return $parent->findAncestorChildByName($child_name);
    }

    /**
     * ArrayAccess interface
    */
    public function offsetExists($offset) {

        if (is_string($offset)) throw new \Exception("Named childs are not supported!");

        return isset($this->children[$offset]);
    }

    /**
     * ArrayAccess interface
    */
    public function offsetGet($offset) {

    	if (is_string($offset)) throw new \Exception("Named childs are not supported!");

        return $this->getChild($offset);
    }

    /**
     * ArrayAccess interface
    */
    public function offsetSet($offset,$child) {

    	if (is_string($offset)) throw new \Exception("Named childs are not supported!");

        $this->setChild($offset,$child);
    }

    /**
     * ArrayAccess interface
    */
    public function offsetUnset($offset) {

        if (is_string($offset)) throw new \Exception("Named childs are not supported!");

        unset($this->children[$offset]);
    }

    //rendering

    function __toString() {
      
        $result = "";
        
        foreach ($this->children as $key => $child) {
            if (is_numeric($key)) {
                
                for ($i=0;$i<LTag::$indent_level;$i++) $result .= "\t";
                
                $result.= "".$child;
            } else throw new \Exception("Named childs should not be present in tag lists!");
        }
        
        return $result;
              
    }
}