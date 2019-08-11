<?php

namespace NLC\Base;

abstract class Enum
{
    private $value;

    final public function __construct($value)
    {
        $c = new \ReflectionClass($this);
        if (!in_array($value, $c->getConstants()) && !isset($c->getConstants()[$value]))
            throw new \InvalidArgumentException("Constant $value not exists");
        $this->value = $value;
    }

    final public function __toString()
    {
        return $this->value;
    }

    /**
     * Get all available enums
     */
    final public static function list()
    {
        $c = new \ReflectionClass(get_called_class());
        return $c->getConstants();
    }
}
