<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\StaticSite;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Filesystem\Filesystem;
use Chrif\Cocotte\Finder\Finder;
use Chrif\Cocotte\Shell\EnvironmentSubstitution\EnvironmentSubstitution;
use Chrif\Cocotte\Shell\EnvironmentSubstitution\SubstitutionFactory;
use Chrif\Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class StaticSiteExporter
{

    /**
     * @var string
     */
    private static $tmpTemplatePath;

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
     * @var StaticSiteNamespace
     */
    private $staticSiteNamespace;

    /**
     * @var StaticSiteHost
     */
    private $staticSiteHost;

    public function __construct(
        Style $style,
        ProcessRunner $processRunner,
        Finder $finder,
        Filesystem $filesystem,
        SubstitutionFactory $substitutionFactory,
        StaticSiteNamespace $staticSiteName,
        StaticSiteHost $staticSiteHost
    ) {
        $this->style = $style;
        $this->processRunner = $processRunner;
        $this->finder = $finder;
        $this->filesystem = $filesystem;
        $this->substitutionFactory = $substitutionFactory;
        $this->staticSiteNamespace = $staticSiteName;
        $this->staticSiteHost = $staticSiteHost;
    }

    public function export()
    {
        $this->style->title('Exporting a new static site to the host filesystem');
        $this->backup();
        $this->copyTemplateToTmp();
        $this->removeIgnoredFiles();
        $this->createDockerComposeOverride();
        $this->createDotEnv();
        $this->substituteEnvInIndexHtml();
        $this->copyTmpToHost();
    }

    public function hostAppPath(): string
    {
        return "/host/{$this->staticSiteNamespace}";
    }

    private function backup(): void
    {
        $this->style->section('Backup');
        if ($this->filesystem->exists($this->hostAppPath())) {
            $this->style->warning("Backing up old '{$this->staticSiteNamespace}' folder on host");
            $this->mustRun(
                [
                    'mv',
                    '-v',
                    $this->hostAppPath(),
                    $this->hostAppPath().'.'.date("YmdHis"),
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
                $this->installerTemplatePath().'/',
                $this->tmpTemplatePath(),
            ]
        );
        $this->mustRun(['mv', '-v', $this->tmpTemplatePath(), $this->tmpAppPath()]);
    }

    private function removeIgnoredFiles(): void
    {
        $this->style->section('Remove ignored files (needed when developing only)');
        $this->mustRun(
            [
                'rm',
                '-fv',
                $this->tmpAppPath()."/.env",
                $this->tmpAppPath()."/.env-override",
                $this->tmpAppPath()."/docker-compose.override.yml",
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
                $this->tmpAppPath()."/docker-compose.override.yml.dist",
                $this->tmpAppPath()."/docker-compose.override.yml",
            ]
        );
    }

    private function createDotEnv(): void
    {
        $this->style->section("Create '.env' and '.env-override' from command options + env");
        EnvironmentSubstitution::withDefaults()
            ->export(
                [
                    'APP_NAME' => $this->staticSiteNamespace->toString(),
                    'APP_HOSTS' => $this->staticSiteHost->toString(),
                ]
            )->substitute(
                $this->substitutionFactory->dumpFile(
                    $this->tmpAppPath().'/.env',
                    EnvironmentSubstitution::formatEnvFile(
                        [
                            'APP_NAME="${APP_NAME}"',
                            'APP_HOSTS="${APP_HOSTS}"',
                            'COCOTTE_MACHINE="${COCOTTE_MACHINE}"',
                            'MACHINE_STORAGE_PATH="${MACHINE_STORAGE_PATH}"',
                        ]
                    )
                )
            );
        EnvironmentSubstitution::withDefaults()
            ->export(
                [
                    'APP_HOSTS' => $this->staticSiteHost->toLocalHostCollection()->toString(),
                ]
            )->substitute(
                $this->substitutionFactory->dumpFile(
                    $this->tmpAppPath().'/.env-override',
                    EnvironmentSubstitution::formatEnvFile(
                        [
                            'APP_HOSTS="${APP_HOSTS}"',
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
                    'APP_NAME' => $this->staticSiteNamespace->toString(),
                ]
            )
            ->restrict(['APP_NAME'])
            ->substitute(
                $this->substitutionFactory->inPlace(
                    $this->finder->exactFile(
                        $this->tmpAppPath().'/web/index.html'
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
                $this->tmpAppPath(),
                '/host',
            ]
        );
        $this->cleanUpTmp();
    }

    private function mustRun($command): void
    {
        $this->processRunner->mustRun(new Process($command));
    }

    private function cleanUpTmp(): void
    {
        $this->mustRun(['rm', '-rfv', $this->tmpAppPath()]);
        $this->mustRun(['rm', '-rfv', $this->tmpTemplatePath()]);
    }

    private function tmpAppPath(): string
    {
        return "/tmp/{$this->staticSiteNamespace}";
    }

    private function tmpTemplatePath(): string
    {
        if (null === self::$tmpTemplatePath) {
            self::$tmpTemplatePath = "/tmp/".uniqid('static-');
        }

        return self::$tmpTemplatePath;
    }

    private function installerTemplatePath(): string
    {
        return "/installer/template/static";
    }
}