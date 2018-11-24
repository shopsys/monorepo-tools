<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use PharIo\Version\Version;
use Shopsys\Releaser\IntervalEvaluator;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Release\Message;

final class ValidateConflictsInComposerJsonReleaseWorker extends AbstractShopsysReleaseWorker
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
     * @param \Shopsys\Releaser\IntervalEvaluator $intervalEvaluator
     */
    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        JsonFileManager $jsonFileManager,
        IntervalEvaluator $intervalEvaluator
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->jsonFileManager = $jsonFileManager;
        $this->intervalEvaluator = $intervalEvaluator;
    }

    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return 'Make sure that "conflict" versions in all composer.json files are closed interval';
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
        $isPassing = true;

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

                $isPassing = false;
            }
        }

        if ($isPassing) {
            $this->symfonyStyle->success(Message::SUCCESS);
        } else {
            $this->symfonyStyle->confirm('Confirm conflict versions are changed to specific versions or closed interval');
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
