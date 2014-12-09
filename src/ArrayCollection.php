<?php
namespace SDS\ArraySupport;

use \ArrayAccess;

class ArrayCollection implements ArrayAccess
{
    protected $array;
    
    public function __construct(array $array = [])
    {
        $this->replaceArray($array);
    }
    
    public function offsetGet($offset)
    {
        return $this->array->offsetGet($offset);
    }
    
    public function offsetSet($offset, $value)
    {
        $this->array->offsetSet($offset, $value);
    }
    
    public function offsetUnset($offset)
    {
        $this->array->offsetUnset($offset);
    }
    
    public function offsetExists($offset)
    {
        return $this->array->offsetExists($offset);
    }
    
    public function replaceArray(array $array)
    {
        $this->array = new Arr($array);
        
        return $this;
    }
    
    public function get($key, $default = null)
    {
        $values = [];
        list($keys, $singular) = $this->normalizeKeyAndValue($key, $default);
        
        foreach ($keys as $key => $default) {
            $values[$key] = $this->array->offsetDeepGet($key, $default);
        }
        
        if ($singular) {
            return empty($values) ? $default : array_pop($values);
        } else {
            $values = $this->convertToMultidimensionalArray($values);
            
            return $values;
        }
    }
    
    public function set($key, $value = null)
    {
        $set = $this->normalizeKeyAndValue($key, $value)[0];
        
        foreach ($set as $key => $value) {
            $this->array->offsetDeepSet($key, $value);
        }
        
        return $this;
    }
    
    public function add($key, $value = null)
    {
        $return = [];
        list($set, $singular) = $this->normalizeKeyAndValue($key, $value);
        
        foreach ($set as $key => $value) {
            if ($this->has($key)) {
                $return[$key] = false;
            } else {
                $return[$key] = true;
                
                $this->set($key, $value);
            }
        }
        
        if ($singular) {
            return empty($return) ? true : array_pop($return);
        } else {
            return $return;
        }
    }
    
    public function replace($key, $value = null)
    {
        $return = [];
        list($set, $singular) = $this->normalizeKeyAndValue($key, $value);
        
        foreach ($set as $key => $value) {
            if (!$this->has($key)) {
                $return[$key] = false;
            } else {
                $return[$key] = true;
                
                $this->set($key, $value);
            }
        }
        
        if ($singular) {
            return empty($return) ? true : array_pop($return);
        } else {
            return $return;
        }
    }
    
    public function remove($key)
    {
        $keys = is_array($key) ? $key : [ $key ];
        
        foreach ($keys as $key) {
            $this->array->offsetDeepUnset($key);
        }
        
        return $this;
    }
    
    public function has($key)
    {
        $keys = is_array($key) ? $key : [ $key ];
        
        foreach ($keys as $key) {
            if (!$this->array->offsetDeepExists($key)) {
                return false;
            }
        }
        
        return true;
    }
    
    protected function normalizeKeyAndValue($key, $value)
    {
        $keys = [];
        $singular = false;
        
        if (is_array($key)) {
            reset($key);
            $firstKey = key($key);
            
            if (is_int($firstKey)) {
                foreach ($key as $k) {
                    $keys[$k] = $value;
                }
            } else {
                foreach ($key as $k => $v) {
                    $keys[$k] = $v;
                }
            }
        } else {
            $singular = true;
            $keys[$key] = $value;
        }
        
        return [ $keys, $singular ];
    }
    
    protected function convertToMultidimensionalArray(array $array)
    {
        $finalArray = new Arr([]);
        
        ksort($array, SORT_STRING);
        
        foreach ($array as $key => $value) {
            $finalArray->offsetDeepSet($key, $value);
        }
        
        return $finalArray->toArray();
    }
}