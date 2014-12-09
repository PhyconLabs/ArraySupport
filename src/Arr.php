<?php
namespace SDS\ArraySupport;

use \ArrayAccess;

class Arr implements ArrayAccess
{
    protected $array;
    
    public function __construct(array $array = [])
    {
        $this->array = $array;
    }
    
    public function offsetGet($offset)
    {
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }
    
    public function offsetDeepGet($offset, $default = null)
    {
        $offsets = explode(".", $offset);
        $lastOffset = array_pop($offsets);
        $currentLevel =& $this->array;
        
        foreach ($offsets as $offset) {
            if (isset($currentLevel[$offset]) && is_array($currentLevel[$offset])) {
                $currentLevel =& $currentLevel[$offset];
            } else {
                return $default;
            }
        }
        
        return isset($currentLevel[$lastOffset]) ? $currentLevel[$lastOffset] : $default;
    }
    
    public function offsetSet($offset, $value)
    {
        $this->array[$offset] = $value;
        
        return $this;
    }
    
    public function offsetDeepSet($offset, $value)
    {
        $offsets = explode(".", $offset);
        $lastOffset = array_pop($offsets);
        $currentLevel =& $this->array;
        
        foreach ($offsets as $offset) {
            if (!isset($currentLevel[$offset]) || !is_array($currentLevel[$offset])) {
                $currentLevel[$offset] = [];
            }
            
            $currentLevel =& $currentLevel[$offset];
        }
        
        $currentLevel[$lastOffset] = $value;
        
        return $this;
    }
    
    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
        
        return $this;
    }
    
    public function offsetDeepUnset($offset)
    {
        $offsets = explode(".", $offset);
        $lastOffset = array_pop($offsets);
        $currentLevel =& $this->array;
        
        foreach ($offsets as $offset) {
            if (!isset($currentLevel[$offset]) || !is_array($currentLevel[$offset])) {
                return $this;
            }
            
            $currentLevel =& $currentLevel[$offset];
        }
        
        unset($currentLevel[$lastOffset]);
        
        return $this;
    }
    
    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }
    
    public function offsetDeepExists($offset)
    {
        return !is_null($this->offsetDeepGet($offset));
    }
    
    public function merge(array ...$arrays)
    {
        foreach ($arrays as $array) {
            $this->array = array_merge($this->array, $array);
        }
        
        return $this;
    }
    
    public function deepMerge(array ...$arrays)
    {
        foreach ($arrays as $array) {
            $this->array = array_replace_recursive($this->array, $array);
        }
        
        return $this;
    }
    
    public function deepAppendMerge(array ...$arrays)
    {
        foreach ($arrays as $array) {
            $this->array = array_merge_recursive($this->array, $array);
        }
        
        return $this;
    }
    
    public function toArray()
    {
        return $this->array;
    }
}