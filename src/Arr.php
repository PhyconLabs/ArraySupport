<?php
namespace SDS\ArraySupport;

use \ArrayObject;

class Arr extends ArrayObject
{
    public function __construct(array $array = [])
    {
        parent::__construct($array);
    }
    
    public function offsetGetDeep($offset, $default = null)
    {
        $offsets = explode(".", $offset);
        $lastOffset = array_pop($offsets);
        $currentLevel =& $this;
        
        foreach ($offsets as $offset) {
            if (isset($currentLevel[$offset]) && is_array($currentLevel[$offset])) {
                $currentLevel =& $currentLevel[$offset];
            } else {
                return $default;
            }
        }
        
        return isset($currentLevel[$lastOffset]) ? $currentLevel[$lastOffset] : $default;
    }
    
    public function offsetExistsDeep($offset)
    {
        return !is_null($this->offsetGetDeep($offset));
    }
}