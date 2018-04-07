<?php declare(strict_types=1);

namespace Chrif\Cocotte\Finder;

final class CocotteFinder extends \Symfony\Component\Finder\Finder implements Finder
{
    public function exactFile(string $realPath): Finder
    {
        $search = new \SplFileInfo($realPath);

        return CocotteFinder::create()->in($search->getPath())->filter(
            function (\SplFileInfo $found) use ($search) {
                return $search->getRealPath() === $found->getRealPath();
            }
        );
    }

}