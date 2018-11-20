<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use PharIo\Version\Version;
use Shopsys\Releaser\IntervalEvaluator;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class ValidateConflictsInComposerJsonReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var bool
     */
    private $isSuccessful = false;

    /**
     * @var string
     */
    private const CONFLICT_SECTION = 'conflict';

    /**
     * @var \Shopsys\Releaser\IntervalEvaluator
     */
    private $intervalEvaluator;

    /**
     * @param \Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider $composerJsonProvider
     * @param \Symplify\MonorepoBuilder\FileSystem\JsonFileManager $jsonFileManager
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Shopsys\Releaser\IntervalEvaluator $intervalEvaluator
     */
    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        JsonFileManager $jsonFileManager,
        SymfonyStyle $symfonyStyle,
        IntervalEvaluator $intervalEvaluator
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->jsonFileManager = $jsonFileManager;
        $this->symfonyStyle = $symfonyStyle;
        $this->intervalEvaluator = $intervalEvaluator;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Validate "require" and "require-dev" for missing "^version" formats';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 900;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        foreach ($this->composerJsonProvider->getRootAndPackageFileInfos() as $fileInfo) {
            $jsonContent = $this->jsonFileManager->loadFromFileInfo($fileInfo);
            if (!isset($jsonContent[self::CONFLICT_SECTION])) {
                continue;
            }

            foreach ($jsonContent[self::CONFLICT_SECTION] as $packageName => $version) {
                if ($this->intervalEvaluator->isClosedInterval($version)) {
                    continue;
                }

                $this->symfonyStyle->warning(sprintf(
                    '"%s" section in "%s" file has open version format for "%s": "%s".%sIt should be closed, e.g. "version|version2".',
                    self::CONFLICT_SECTION,
                    $fileInfo->getPathname(),
                    $packageName,
                    $version,
                    PHP_EOL
                ));

                $this->isSuccessful = false;
            }
        }

        if ($this->isSuccessful) {
            $this->symfonyStyle->success('All versions in "conflicts" are in correct closed format');
        }
    }
}
