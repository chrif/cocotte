<?php declare(strict_types=1);

namespace Cocotte\Collection;

use ArrayIterator;
use IteratorAggregate;

abstract class GenericCollection implements IteratorAggregate, \Countable
{

    protected $values;

    /**
     * ex.:
     *
     * public function __construct(string ...$strings)
     * {
     *      $this->values = $strings;
     * }
     */
    abstract public function __construct();

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

    public function count()
    {
        return count($this->values);
    }

}