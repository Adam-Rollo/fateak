<?php
/**
 * Fateak - Rollo
 * For View output ease
 */
class Fateak_Viewobject implements arrayaccess
{
    protected $container = array();

    public function __construct($src)
    {
        $this->container = $src;
    }

    public function __get($key)
    {
        if (! isset($this->container[$key]))
        {
            return null;
        }
        else
        {
            return $this->container[$key];
        }
    }

    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) 
        {
            $this->container[] = $value;
        } 
        else 
        {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) 
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) 
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) 
    {
        echo $offset;
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}
