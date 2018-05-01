<?php declare(strict_types=1);

namespace Cocotte\Test\PHPUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

final class ParameterGrabber extends Constraint
{
    private $lastValue;

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return 'is saving its last value';
    }

    public function value()
    {
        return $this->lastValue;
    }

    /**
     * Evaluates the constraint for parameter $value. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param mixed $other value or object to evaluate
     * @return bool
     */
    protected function matches($other): bool
    {
        $this->lastValue = $other;

        return true;
    }
}