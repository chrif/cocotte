<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

use Symfony\Component\Console\Style\SymfonyStyle;

final class CocotteStyle extends SymfonyStyle implements Style
{

    public function ok($message)
    {
        $this->block($message, 'OK', null, ' ', true);
    }

}