<?php

namespace Cocotte\Help;

interface CommandExamples
{
    public function install(): string;

    public function installInteractive(): string;

    public function uninstall(): string;

    public function uninstallInteractive(): string;

    public function staticSite(): string;

    public function staticSiteInteractive(): string;
}