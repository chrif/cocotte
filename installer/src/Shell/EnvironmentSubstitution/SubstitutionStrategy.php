<?php

namespace Chrif\Cocotte\Shell\EnvironmentSubstitution;

use Symfony\Component\Process\Process;

interface SubstitutionStrategy
{

    public function substitute(Process $envSubstProcess);

}