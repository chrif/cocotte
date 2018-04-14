<?php declare(strict_types=1);

namespace Chrif\Cocotte\Host;

use Assert\Assertion;
use Chrif\Cocotte\Console\Style;

final class HostMount
{

    /**
     * @var Mounts
     */
    private $mounts;

    /**
     * @var Style
     */
    private $style;

    public function __construct(Mounts $mounts, Style $style)
    {
        $this->mounts = $mounts;
        $this->style = $style;
    }

    public function assertMounted()
    {
        foreach ($this->mounts->toArray() as $mount) {
            if ('bind' === $mount['Type'] && '/host' === $mount['Destination']) {
                Assertion::true($mount['RW'], "Volume /host must be writable");
                $this->style->ok("A writable bind mount exists for the destination '/host'");
                return;
            }
        }
        throw new \Exception("There is no writable bind mount with the destination '/host");
    }
}