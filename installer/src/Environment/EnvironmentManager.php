<?php declare(strict_types=1);

namespace Chrif\Cocotte\Environment;

use Symfony\Component\Console\Input\InputInterface;

final class EnvironmentManager
{
    /**
     * @var ImportableValue[]
     */
    private $importableValues;

    /**
     * @var ExportableValue[]
     */
    private $exportableValues;

    public function addImportableValue(ImportableValue $importableValue)
    {
        $this->importableValues[] = $importableValue;
    }

    public function addExportableValue(ExportableValue $exportableValue)
    {
        $this->exportableValues[] = $exportableValue;
    }

    public function exportFromInput(InputInterface $input)
    {
        foreach ($this->exportableValues as $exportableValue) {
            if ($exportableValue instanceof InputOptionValue) {
                if ($input->hasOption($exportableValue::inputOptionName())) {
                    $value = $input->getOption($exportableValue::inputOptionName());
                    if (null !== $value) {
                        $exportableValue::toEnv($value);
                    }
                }
            }
        }
    }
}