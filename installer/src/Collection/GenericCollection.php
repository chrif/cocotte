<?php declare(strict_types=1);

namespace Chrif\Cocotte\Collection;

use ArrayIterator;
use IteratorAggregate;

abstract class GenericCollection implements IteratorAggregate, \Countable, \ArrayAccess
{

    protected $values;

    /**
     * @param $values
     * @return static
     * @throws \ReflectionException
     */
    public static function fromArray($values)
    {
        $reflection = new \ReflectionClass(static::class);

        /** @var GenericCollection $newInstanceArgs */
        $newInstanceArgs = $reflection->newInstanceArgs($values);

        return $newInstanceArgs;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }

    public function each(callable $callable)
    {
        foreach ($this->values as $value) {
            $ret = call_user_func($callable, $value);
            if ($ret === false) {
                return;
            }
        }
    }

    public function count()
    {
        return count($this->values);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->values);
    }

    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    public function toArray(): array
    {
        return $this->values;
    }
}