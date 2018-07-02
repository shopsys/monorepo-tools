<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Finder;

use IteratorAggregate;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;

final class FileFinder implements CustomSourceProviderInterface
{
    /**
     * @param string[] $source
     * @return IteratorAggregate
     */
    public function find(array $source): IteratorAggregate
    {
        $directories = [];
        $files = [];
        foreach ($source as $singleSource) {
            if (is_file($singleSource)) {
                $fileInfo = new SplFileInfo($singleSource);
                $files[$fileInfo->getPath()] = new SymfonySplFileInfo($singleSource, $fileInfo->getPath(), $fileInfo->getPathname());
            } else {
                $directories[] = $singleSource;
            }
        }

        $finder = Finder::create()->files()
            ->name('#\.(twig|html(\.twig)?|php)$#')
            ->in($directories);

        // ArrayIterator will be fixed in new release
        $finder->append(new \ArrayIterator($files));

        return $finder;
    }
}
