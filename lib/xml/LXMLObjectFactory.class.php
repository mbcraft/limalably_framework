<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LXMLObjectFactory
{
    private $mappings = array();

    private static $factories = array();

    static function initFactory($name,$mappings)
    {
        if (!isset(self::$factories[$name]))
            self::$factories[$name] = new LXMLObjectFactory ();

        $factory = self::$factories[$name];
        foreach ($mappings as $key => $value)
            $factory->addClassMapping($key,$value);
    }

    static function getFactory($name)
    {
        return self::$factories[$name];
    }

    private function addClassMapping($tag_name,$class_name)
    {
        $this->mappings[$tag_name] = $class_name;
    }

    private function createXMLObject($tag_name,$attributes)
    {
        $class_name = $this->mappings[$tag_name];

        $tag = new $class_name();
        $tag->setAttributes($attributes);

        return $tag;
    }

    function parseXMLFile($file)
    {
        if ($file instanceof LFile)
        {
            return $this->parseXMLString($file->getContent());
        }
    }

    function parseXMLString($xml)
    {
        $root = new SimpleXMLElement(trim($xml));  //uso il trim per sicurezza.
        return $this->internalParseXML($root);
    }

    private function internalParseXML($simple_xml_element)
    {
        $ob = $this->createXMLObject($simple_xml_element->getName(), $simple_xml_element->attributes());

        foreach($simple_xml_element->children() as $child)
        {
            $ob->addChild($this->internalParseXML($child));
        }

        return $ob;
    }
}