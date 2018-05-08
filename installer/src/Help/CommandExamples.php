<?php

namespace Cocotte\Help;

interface CommandExamples
{
    public function install(): string;

    public function uninstall(): string;

    public function staticSite(): string;
}