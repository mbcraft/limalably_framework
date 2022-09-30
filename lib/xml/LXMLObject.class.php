<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LXMLObject
{
    private $attributes;
    private $childs = array();

    function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    function addChild($child)
    {
        $this->childs[] = $child;
        
    }

    function getChilds()
    {
        return $this->childs;
    }

    function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    function getAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    function setAttribute($key,$value)
    {
        $this->attributes[$key] = $value;
    }
}

?>