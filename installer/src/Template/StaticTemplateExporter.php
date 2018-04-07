<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Filesystem\Filesystem;
use Chrif\Cocotte\Finder\Finder;
use Chrif\Cocotte\Shell\EnvironmentSubstitution;
use Chrif\Cocotte\Shell\EnvironmentSubstitution\SubstitutionFactory;
use Chrif\Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class StaticTemplateExporter
{

    /**
     * @var Style
     */
    private $style;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SubstitutionFactory
     */
    private $substitutionFactory;

    /**
     * @var StaticTemplateConfiguration
     */
    private $config;

    public function __construct(
        Style $style,
        ProcessRunner $processRunner,
        Finder $finder,
        Filesystem $filesystem,
        SubstitutionFactory $substitutionFactory
    ) {
        $this->style = $style;
        $this->processRunner = $processRunner;
        $this->finder = $finder;
        $this->filesystem = $filesystem;
        $this->substitutionFactory = $substitutionFactory;
    }

    public function export(StaticTemplateConfiguration $config)
    {
        $this->config = $config;
        $this->style->title('Exporting static template to host');
        $this->backup();
        $this->copyTemplateToTmp();
        $this->removeIgnoredFiles();
        $this->createDockerComposeOverride();
        $this->createDotEnv();
        $this->substituteEnvInIndexHtml();
        $this->copyTmpToHost();
    }

    private function backup(): void
    {
        $this->style->section('Backup');
        if ($this->filesystem->exists($this->config->hostAppPath())) {
            $this->style->warning("Backing up old '{$this->config->appName()}' folder on host");
            $this->mustRun(
                [
                    'mv',
                    '-v',
                    $this->config->hostAppPath(),
                    $this->config->hostAppPath().date("YmdHis"),
                ]
            );
        } else {
            $this->style->success('No backup was necessary');
        }
    }

    private function copyTemplateToTmp(): void
    {
        $this->style->section('Copy template to tmp directory');
        $this->cleanUpTmp();
        $this->mustRun(
            [
                'rsync',
                '-rv',
                $this->config->installerTemplatePath().'/',
                $this->config->tmpTemplatePath(),
            ]
        );
        $this->mustRun(['mv', '-v', $this->config->tmpTemplatePath(), $this->config->tmpAppPath()]);
    }

    private function removeIgnoredFiles(): void
    {
        $this->style->section('Remove ignored files (needed when developing only)');
        $this->mustRun(
            [
                'rm',
                '-fv',
                $this->config->tmpAppPath()."/.env",
                $this->config->tmpAppPath()."/.env-override",
                $this->config->tmpAppPath()."/docker-compose.override.yml",
            ]
        );
    }

    private function createDockerComposeOverride(): void
    {
        $this->style->section('Create ignored docker-compose.override.yml from dist');
        $this->mustRun(
            [
                'cp',
                '-v',
                $this->config->tmpAppPath()."/docker-compose.override.yml.dist",
                $this->config->tmpAppPath()."/docker-compose.override.yml",
            ]
        );
    }

    private function createDotEnv(): void
    {
        $this->style->section("Create '.env' and '.env-override' from command options + env");
        EnvironmentSubstitution::withDefaults()
            ->export(
                [
                    'APP_NAME' => $this->config->appName()->value(),
                    'APP_HOSTS' => $this->config->appHosts()->toString(),
                ]
            )->substitute(
                $this->substitutionFactory->dumpFile(
                    $this->config->tmpAppPath().'/.env',
                    EnvironmentSubstitution::formatEnvFile(
                        [
                            'APP_NAME=${APP_NAME}',
                            'APP_HOSTS=${APP_HOSTS}',
                            'COCOTTE_MACHINE=${COCOTTE_MACHINE}',
                            'MACHINE_STORAGE_PATH=${MACHINE_STORAGE_PATH}',
                        ]
                    )
                )
            );
        EnvironmentSubstitution::withDefaults()
            ->export(
                [
                    'APP_HOSTS' => $this->config->appHosts()->toLocal()->toString(),
                ]
            )->substitute(
                $this->substitutionFactory->dumpFile(
                    $this->config->tmpAppPath().'/.env-override',
                    EnvironmentSubstitution::formatEnvFile(
                        [
                            'APP_HOSTS=${APP_HOSTS}',
                        ]
                    )
                )
            );
    }

    private function substituteEnvInIndexHtml(): void
    {
        $this->style->section('Substitute appName in template index.html');
        EnvironmentSubstitution::withDefaults()
            ->export(
                [
                    'APP_NAME' => $this->config->appName()->value(),
                ]
            )
            ->restrict(['APP_NAME'])
            ->substitute(
                $this->substitutionFactory->inPlace(
                    $this->finder->exactFile(
                        $this->config->tmpAppPath().'/web/index.html'
                    )
                )
            );
    }

    private function copyTmpToHost(): void
    {
        $this->style->section('Copy tmp to host');
        $this->mustRun(
            [
                'rsync',
                '-rv',
                $this->config->tmpAppPath(),
                '/host',
            ]
        );
        $this->cleanUpTmp();
    }

    private function mustRun($command)
    {
        $this->processRunner->mustRun(new Process($command));
    }

    private function cleanUpTmp(): void
    {
        $this->mustRun(['rm', '-rfv', $this->config->tmpAppPath()]);
        $this->mustRun(['rm', '-rfv', $this->config->tmpTemplatePath()]);
    }

}