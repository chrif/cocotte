<?php declare(strict_types=1);

namespace Cocotte\Test\Double\Console;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

final class HasAllOptionsWithNullValuesInput implements InputInterface
{
    public function getFirstArgument()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function hasParameterOption($values, $onlyParams = false)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function getParameterOption($values, $default = false, $onlyParams = false)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function bind(InputDefinition $definition)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function validate()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function getArguments()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function getArgument($name)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function setArgument($name, $value)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function hasArgument($name)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function getOptions()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function getOption($name)
    {
        return null;
    }

    public function setOption($name, $value)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function hasOption($name)
    {
        return true;
    }

    public function isInteractive()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function setInteractive($interactive)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

}