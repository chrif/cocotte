<?php declare(strict_types=1);

namespace Chrif\Cocotte\Filesystem;

final class CocotteFilesystem extends \Symfony\Component\Filesystem\Filesystem implements Filesystem
{
    public static function create(): Filesystem
    {
        return new self();
    }
}