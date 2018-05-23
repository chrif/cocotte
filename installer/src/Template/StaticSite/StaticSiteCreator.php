<?php declare(strict_types=1);

namespace Cocotte\Template\StaticSite;

use Cocotte\Console\Style;
use Cocotte\Filesystem\Filesystem;
use Cocotte\Finder\Finder;
use Cocotte\Shell\EnvironmentSubstitution\EnvironmentSubstitution;
use Cocotte\Shell\EnvironmentSubstitution\SubstitutionFactory;
use Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class StaticSiteCreator
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
     * @var StaticSiteNamespace
     */
    private $staticSiteNamespace;

    /**
     * @var StaticSiteHostname
     */
    private $staticSiteHostname;

    /**
     * @var string
     */
    private $tmpTemplatePath;

    public function __construct(
        Style $style,
        ProcessRunner $processRunner,
        Finder $finder,
        Filesystem $filesystem,
        SubstitutionFactory $substitutionFactory,
        StaticSiteNamespace $staticSiteName,
        StaticSiteHostname $staticSiteHostname
    ) {
        $this->style = $style;
        $this->processRunner = $processRunner;
        $this->finder = $finder;
        $this->filesystem = $filesystem;
        $this->substitutionFactory = $substitutionFactory;
        $this->staticSiteNamespace = $staticSiteName;
        $this->staticSiteHostname = $staticSiteHostname;
        $this->tmpTemplatePath = "/tmp/".uniqid('static-');
    }

    public function create()
    {
        $this->backup();
        $this->copyTemplateToTmp();
        $this->removeIgnoredFiles();
        $this->createDockerComposeOverride();
        $this->createDotEnv();
        $this->substituteEnvInIndexHtml();
        $this->copyTmpToHost();
        $this->chmodBin();
    }

    public function hostAppPath(): string
    {
        return "/host/{$this->staticSiteNamespace}";
    }

    private function backup(): void
    {
        $this->style->verbose('Backup');
        if ($this->filesystem->exists($this->hostAppPath())) {
            $this->style->note("Backing up old '{$this->staticSiteNamespace}' folder on host filesystem");
            $this->mustRun(
                [
                    'mv',
                    '-v',
                    $this->hostAppPath(),
                    $this->hostAppPath().'.'.date("YmdHis"),
                ]
            );
        } else {
            $this->style->veryVerbose('No backup was necessary');
        }
    }

    private function copyTemplateToTmp(): void
    {
        $this->style->verbose('Copy template to tmp directory');
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
        $this->style->verbose('Remove ignored files (needed when developing only)');
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
        $this->style->verbose('Create ignored docker-compose.override.yml from dist');
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
        $this->style->verbose("Create '.env' and '.env-override' from command options + env");
        $this->createDotEnvProd();
        $this->createDotEnvDev();
    }

    private function substituteEnvInIndexHtml(): void
    {
        $this->style->verbose('Substitute appName in template index.html');
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
        $this->style->verbose('Copy tmp to host filesystem');
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

    private function chmodBin()
    {
        $this->mustRun("chmod +x {$this->hostAppPath()}/bin/*");
    }

    private function tmpAppPath(): string
    {
        return "/tmp/{$this->staticSiteNamespace}";
    }

    private function tmpTemplatePath(): string
    {
        return $this->tmpTemplatePath;
    }

    private function installerTemplatePath(): string
    {
        return "/installer/template/static";
    }

    private function createDotEnvProd(): void
    {
        EnvironmentSubstitution::withDefaults()
            ->export(
                [
                    'APP_NAME' => $this->staticSiteNamespace->toString(),
                    'APP_HOSTS' => $this->staticSiteHostname->toString(),
                ]
            )->substitute(
                $this->substitutionFactory->dumpFile(
                    $this->tmpAppPath().'/.env',
                    EnvironmentSubstitution::formatEnvFile(
                        [
                            'APP_NAME="${APP_NAME}"',
                            'APP_HOSTS="${APP_HOSTS}"',
                            'MACHINE_NAME="${MACHINE_NAME}"',
                            'MACHINE_STORAGE_PATH="${MACHINE_STORAGE_PATH}"',
                        ]
                    )
                )
            );
    }

    private function createDotEnvDev(): void
    {
        EnvironmentSubstitution::withDefaults()
            ->export(
                [
                    'APP_HOSTS' => $this->staticSiteHostname->toLocal()->toString(),
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
}