<?php

declare(strict_types=1);

namespace Shopsys\Releaser\FilesProvider;

use Symfony\Component\Finder\Finder;

final class DockerComposeFilesProvider
{
    /**
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function provide(): array
    {
        $finder = Finder::create()
            ->in(getcwd())
            ->exclude('vendor')
            ->files()
            ->name('#docker-compose([\w-]+)?\.yml\.dist$#');

        return iterator_to_array($finder->getIterator());
    }
}
