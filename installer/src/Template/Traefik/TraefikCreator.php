<?php declare(strict_types=1);

namespace Cocotte\Template\Traefik;

use Cocotte\Console\Style;
use Cocotte\Filesystem\Filesystem;
use Cocotte\Shell\BasicAuth;
use Cocotte\Shell\EnvironmentSubstitution\EnvironmentSubstitution;
use Cocotte\Shell\EnvironmentSubstitution\SubstitutionFactory;
use Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class TraefikCreator
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
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SubstitutionFactory
     */
    private $substitutionFactory;

    /**
     * @var TraefikHostname
     */
    private $traefikHostname;

    /**
     * @var TraefikPassword
     */
    private $traefikPassword;

    /**
     * @var TraefikUsername
     */
    private $traefikUsername;

    /**
     * @var BasicAuth
     */
    private $basicAuth;

    public function __construct(
        Style $style,
        ProcessRunner $processRunner,
        Filesystem $filesystem,
        SubstitutionFactory $substitutionFactory,
        TraefikHostname $traefikHostname,
        TraefikPassword $traefikPassword,
        TraefikUsername $traefikUsername,
        BasicAuth $basicAuth
    ) {
        $this->style = $style;
        $this->processRunner = $processRunner;
        $this->filesystem = $filesystem;
        $this->substitutionFactory = $substitutionFactory;
        $this->traefikHostname = $traefikHostname;
        $this->traefikPassword = $traefikPassword;
        $this->traefikUsername = $traefikUsername;
        $this->basicAuth = $basicAuth;
    }

    public function create()
    {
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
        $this->style->verbose('Backup');
        if ($this->filesystem->exists($this->hostAppPath())) {
            $this->style->note("Backing up old 'traefik' folder on host filesystem");
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

        $basicAuth = $this->basicAuth->generate(
            $this->traefikUsername->toString(),
            $this->traefikPassword->toString()
        );

        EnvironmentSubstitution::withDefaults()
            ->restrict(
                [
                    'TRAEFIK_UI_HOSTNAME',
                    'TRAEFIK_ACME_EMAIL',
                    'MACHINE_NAME',
                    'MACHINE_STORAGE_PATH',
                ]
            )
            ->substitute(
                $this->substitutionFactory->dumpFile(
                    $this->tmpAppPath().'/.env',
                    EnvironmentSubstitution::formatEnvFile(
                        [
                            'APP_HOSTS="${TRAEFIK_UI_HOSTNAME}"',
                            "APP_AUTH_BASIC='{$basicAuth}'",
                            'ACME_EMAIL="${TRAEFIK_ACME_EMAIL}"',
                            'MACHINE_NAME="${MACHINE_NAME}"',
                            'MACHINE_STORAGE_PATH="${MACHINE_STORAGE_PATH}"',
                        ]
                    )
                )
            );
        EnvironmentSubstitution::withDefaults()
            ->export(
                [
                    'APP_HOSTS' => $this->traefikHostname->toLocal()->toString(),
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