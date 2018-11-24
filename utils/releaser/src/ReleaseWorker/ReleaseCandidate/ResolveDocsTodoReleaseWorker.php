<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Nette\Utils\Strings;
use PharIo\Version\Version;
use Shopsys\Releaser\Stage;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareReleaseWorkerInterface;

final class ResolveDocsTodoReleaseWorker implements ReleaseWorkerInterface, StageAwareReleaseWorkerInterface
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var string
     */
    private const TODO_PLACEHOLDER = '<!--- TODO';

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     */
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Resolve TODO comments in *.md files';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 840;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        // @todo for fast development
        return;

        $finder = Finder::create()->files()
            ->name('*.md')
            ->in(getcwd())
            ->exclude('vendor');

        $this->symfonyStyle->section(sprintf('Checking %d files for "%s"', count($finder->getIterator()), self::TODO_PLACEHOLDER));

        /** @var \Symfony\Component\Finder\SplFileInfo $fileInfo */
        foreach ($finder as $fileInfo) {
            $todoFound = Strings::matchAll($fileInfo->getContents(), '#' . preg_quote(self::TODO_PLACEHOLDER) . '#');
            if ($todoFound === []) {
                continue;
            }

            // @todo add clickable file links later: https://github.com/symfony/symfony/pull/29168/files
            $this->symfonyStyle->note(sprintf(
                '[Manual] File "%s" has %d todo%s to resolve. Fix them manually.',
                $fileInfo->getPathname(),
                count($todoFound),
                $todoFound > 1 ? 's' : ''
            ));
        }
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
