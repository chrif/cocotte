<?php declare(strict_types=1);

namespace Chrif\Cocotte\Environment;

use Chrif\Cocotte\Console\Style;
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

    /**
     * @var Style
     */
    private $style;

    public function __construct(Style $style)
    {
        $this->style = $style;
    }

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
        $this->style->title("Exporting console input to environment variables");
        foreach ($this->exportableValues as $exportableValue) {
            if ($exportableValue instanceof InputOptionValue) {
                $inputOptionName = $exportableValue::inputOptionName();
                if ($input->hasOption($inputOptionName)) {
                    $value = $input->getOption($inputOptionName);
                    if (null !== $value) {
                        $this->style->writeln("Exporting '$inputOptionName' with value '$value'");
                        $exportableValue::toEnv($value);
                    }
                }
            }
        }
        $this->style->ok(print_r(getenv(), true));
    }
}