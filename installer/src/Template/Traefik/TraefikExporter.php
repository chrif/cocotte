<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Filesystem\Filesystem;
use Chrif\Cocotte\Finder\Finder;
use Chrif\Cocotte\Shell\BasicAuth;
use Chrif\Cocotte\Shell\EnvironmentSubstitution\EnvironmentSubstitution;
use Chrif\Cocotte\Shell\EnvironmentSubstitution\SubstitutionFactory;
use Chrif\Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class TraefikExporter
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
     * @var TraefikUiHostname
     */
    private $traefikUiHostname;

    /**
     * @var TraefikUiPassword
     */
    private $traefikUiPassword;

    /**
     * @var TraefikUiUsername
     */
    private $traefikUiUsername;

    /**
     * @var \Chrif\Cocotte\Shell\BasicAuth
     */
    private $basicAuth;

    public function __construct(
        Style $style,
        ProcessRunner $processRunner,
        Finder $finder,
        Filesystem $filesystem,
        SubstitutionFactory $substitutionFactory,
        TraefikUiHostname $traefikUiHostname,
        TraefikUiPassword $traefikUiPassword,
        TraefikUiUsername $traefikUiUsername,
        BasicAuth $basicAuth
    ) {
        $this->style = $style;
        $this->processRunner = $processRunner;
        $this->finder = $finder;
        $this->filesystem = $filesystem;
        $this->substitutionFactory = $substitutionFactory;
        $this->traefikUiHostname = $traefikUiHostname;
        $this->traefikUiPassword = $traefikUiPassword;
        $this->traefikUiUsername = $traefikUiUsername;
        $this->basicAuth = $basicAuth;
    }

    public function export()
    {
        $this->style->title('Exporting traefik template to host');
        $this->backup();
        $this->copyTemplateToTmp();
        $this->removeIgnoredFiles();
        $this->createDockerComposeOverride();
        $this->createDotEnv();
        $this->copyTmpToHost();
    }

    public function hostAppPath(): string
    {
        return "/host/traefik";
    }

    private function backup(): void
    {
        $this->style->section('Backup');
        if ($this->filesystem->exists($this->hostAppPath())) {
            $this->style->warning("Backing up old 'traefik' folder on host");
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

        $basicAuth = $this->basicAuth->generate(
            $this->traefikUiUsername->value(),
            $this->traefikUiPassword->value()
        );

        EnvironmentSubstitution::withDefaults()
            ->export(
                [
                    'APP_HOSTS' => $this->traefikUiHostname->toString(),
                ]
            )->substitute(
                $this->substitutionFactory->dumpFile(
                    $this->tmpAppPath().'/.env',
                    EnvironmentSubstitution::formatEnvFile(
                        [
                            'APP_HOSTS="${TRAEFIK_UI_HOSTNAME}"',
                            "APP_AUTH_BASIC='{$basicAuth}'",
                            'ACME_EMAIL="${TRAEFIK_ACME_EMAIL:-}"',
                            'COCOTTE_MACHINE="${COCOTTE_MACHINE}"',
                            'MACHINE_STORAGE_PATH="${MACHINE_STORAGE_PATH}"',
                        ]
                    )
                )
            );
        EnvironmentSubstitution::withDefaults()
            ->export(
                [
                    'APP_HOSTS' => $this->traefikUiHostname->toLocalHostCollection()->toString(),
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

    private function mustRun($command)
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
        return "/tmp/traefik";
    }

    private function tmpTemplatePath(): string
    {
        if (null === self::$tmpTemplatePath) {
            self::$tmpTemplatePath = "/tmp/".uniqid('traefik-');
        }

        return self::$tmpTemplatePath;
    }

    private function installerTemplatePath(): string
    {
        return "/installer/template/traefik";
    }
}