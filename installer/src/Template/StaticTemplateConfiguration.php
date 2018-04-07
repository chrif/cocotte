<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template;

use Chrif\Cocotte\Configuration\AppHostCollection;
use Chrif\Cocotte\Configuration\AppName;

final class StaticTemplateConfiguration
{
    /**
     * @var string
     */
    private $hostAppPath;
    /**
     * @var string
     */
    private $tmpAppPath;
    /**
     * @var string
     */
    private $tmpTemplatePath;
    /**
     * @var string
     */
    private $installerTemplatePath;

    /**
     * @var AppName
     */
    private $appName;

    /**
     * @var AppHostCollection
     */
    private $appHosts;

    public function __construct(
        string $hostAppPath,
        string $tmpAppPath,
        string $tmpTemplatePath,
        string $installerTemplatePath,
        AppName $appName,
        AppHostCollection $appHosts
    ) {
        $this->hostAppPath = $hostAppPath;
        $this->tmpAppPath = $tmpAppPath;
        $this->tmpTemplatePath = $tmpTemplatePath;
        $this->installerTemplatePath = $installerTemplatePath;
        $this->appName = $appName;
        $this->appHosts = $appHosts;
    }

    public static function forApp(AppName $appName, AppHostCollection $appHostCollection)
    {
        return new self(
            "/host/{$appName}",
            "/tmp/{$appName}",
            "/tmp/".uniqid('static-'),
            "/installer/template/static",
            $appName,
            $appHostCollection
        );
    }

    /**
     * @return string
     */
    public function hostAppPath(): string
    {
        return $this->hostAppPath;
    }

    /**
     * @return string
     */
    public function tmpAppPath(): string
    {
        return $this->tmpAppPath;
    }

    /**
     * @return string
     */
    public function tmpTemplatePath(): string
    {
        return $this->tmpTemplatePath;
    }

    /**
     * @return string
     */
    public function installerTemplatePath(): string
    {
        return $this->installerTemplatePath;
    }

    /**
     * @return AppName
     */
    public function appName()
    {
        return $this->appName;
    }

    /**
     * @return AppHostCollection
     */
    public function appHosts()
    {
        return $this->appHosts;
    }

}