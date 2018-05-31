<?php declare(strict_types=1);

namespace Cocotte\Filesystem;

class CocotteFilesystem extends \Symfony\Component\Filesystem\Filesystem implements Filesystem
{
    public function isLink(string $filename): bool
    {
        return is_link($filename);
    }

}