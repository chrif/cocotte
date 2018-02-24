<?php

declare(strict_types=1);


namespace App\Lib;

use Symfony\Component\HttpFoundation\File\File;

class HostFileSystem
{
    public function __construct()
    {
        if (!$this->directoryExists()) {
            throw new \Exception('host directory not found');
        }
    }

    public static function get(): HostFileSystem
    {
        return new self();
    }

    public function directoryExists(): bool
    {
        return is_dir($this->directory());
    }

    public function directory(): string
    {
        return __DIR__."/../../host";
    }

    public function createFile($filename, $content): File
    {
        $path = $this->path($filename);
        if (false === file_put_contents($path, $content)) {
            throw new \Exception('Could not write to host file system: '.$filename);
        }

        return new File($path);
    }

    public function path($filename): string
    {
        return $this->directory()."/".$filename;
    }
}