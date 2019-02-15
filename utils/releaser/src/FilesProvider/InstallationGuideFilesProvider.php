<?php

declare(strict_types=1);

namespace Shopsys\Releaser\FilesProvider;

use Symfony\Component\Finder\Finder;

final class InstallationGuideFilesProvider
{
    /**
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function provide(): array
    {
        $finder = Finder::create()->files()
            ->name('installation-using*.md')
            ->in(getcwd() . '/docs/installation');

        return iterator_to_array($finder->getIterator());
    }
}
